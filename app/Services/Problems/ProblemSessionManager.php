<?php

namespace App\Services\Problems;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\LabSession;
use App\Models\User;
use App\Jobs\ProvisionProblemSessionJob;
use App\Services\K8s\K8sClient;
use App\Services\K8s\HelmClient;
use App\Services\K8s\IngressUrlBuilder;
use App\Services\LabRuntime\SessionProvisioner;
use App\Services\LabRuntime\SessionDestroyer;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Manages the lifecycle of problem sessions:
 * - Provisions or reuses a lightweight vcluster
 * - Applies scenario manifests (pre-broken state)
 * - Provides kubectl proxy for real terminal commands
 * - Runs validation on submit
 * - Cleans up after completion
 */
class ProblemSessionManager
{
    public function __construct(
        protected K8sClient $k8sClient,
        protected HelmClient $helmClient,
        protected IngressUrlBuilder $ingressUrlBuilder,
        protected SessionProvisioner $sessionProvisioner,
        protected ClusterValidatorService $validator,
        protected SessionDestroyer $sessionDestroyer,
    ) {}

    /**
     * Start a problem session: provision vcluster + apply scenario.
     *
     * Reuses the user's existing lab session if one is running,
     * otherwise provisions a new lightweight one.
     *
     * @return array ['status' => string, 'session_id' => string, 'message' => string]
     */
    public function startSession(User $user, Challenge $challenge, ChallengeAttempt $attempt): array
    {
        if (!$challenge->needsCluster()) {
            return [
                'status' => 'ready',
                'session_id' => null,
                'message' => 'No cluster needed for this problem type.',
            ];
        }

        try {
            // Check if user already has an active lab session we can reuse
            $existingSession = $user->labSessions()
                ->active()
                ->latest()
                ->first();

            if ($existingSession && $existingSession->isRunning()) {
                Log::info("Reusing existing lab session for problem", [
                    'session_id' => $existingSession->id,
                    'challenge' => $challenge->slug,
                ]);

                // Clean the vcluster before applying new problem's scenario
                $this->cleanVclusterNamespace($existingSession);

                // Apply scenario manifests to the existing vcluster
                $this->applyScenario($challenge, $existingSession);

                // Link attempt to session
                $attempt->update(['lab_session_id' => $existingSession->id]);

                return [
                    'status' => 'ready',
                    'session_id' => $existingSession->id,
                    'message' => 'Environment ready.',
                ];
            }

            // Check if there's already a provisioning session for this user
            $provisioningSession = $user->labSessions()
                ->where('status', LabSession::STATUS_PROVISIONING)
                ->latest()
                ->first();

            if ($provisioningSession) {
                $attempt->update(['lab_session_id' => $provisioningSession->id]);
                return [
                    'status' => 'provisioning',
                    'session_id' => $provisioningSession->id,
                    'message' => 'Environment is being provisioned...',
                ];
            }

            // Create session record and dispatch async provisioning job
            $session = $this->createProblemSession($user, $challenge);
            $attempt->update(['lab_session_id' => $session->id]);

            // Dispatch the provisioning job (runs in queue worker like courses)
            ProvisionProblemSessionJob::dispatch($session->id, $challenge->slug);

            return [
                'status' => 'provisioning',
                'session_id' => $session->id,
                'message' => 'Environment is being provisioned...',
            ];

        } catch (Exception $e) {
            Log::error("Failed to start problem session", [
                'challenge' => $challenge->slug,
                'user' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'status' => 'error',
                'session_id' => null,
                'message' => 'Failed to start environment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Execute a kubectl command in the user's vcluster.
     * Routes through the host cluster: kubectl exec into the vcluster pod.
     *
     * @return array ['output' => string, 'exit_code' => int]
     */
    public function executeCommand(LabSession $session, string $command, string $category = 'kubernetes'): array
    {
        // Security: validate and sanitize the command for this category
        $sanitized = $this->sanitizeCommand($command, $category);

        if ($sanitized === null) {
            $allowed = match ($category) {
                'docker' => 'docker commands',
                'terraform' => 'terraform commands',
                'linux' => 'shell commands',
                default => 'kubectl commands',
            };
            return [
                'output' => "Error: Command not allowed. Only {$allowed} are permitted.",
                'exit_code' => 1,
            ];
        }

        // Determine runner pod and command prefix per category
        [$runnerPod, $execPrefix] = match ($category) {
            'docker' => ['docker-runner', ''],
            'terraform' => ['terraform-runner', ''],
            'linux' => ['linux-runner', ''],
            default => ['kubectl-runner', 'kubectl '],
        };

        // For K8s: intercept kubectl apply -f <filename>
        if ($category === 'kubernetes' && preg_match('/^apply\s+-f\s+(?!-\s*$)\S+/i', $sanitized)) {
            return [
                'output' => "⚠️  Files from the editor don't exist on the cluster.\n" .
                            "Use the \$ kubectl apply button above the editor, or run:\n" .
                            "  kubectl apply -f -\n" .
                            "and paste your YAML (then press Ctrl+D to apply).",
                'exit_code' => 1,
            ];
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');
        $namespace = $session->host_namespace;

        // Build the exec command: kubectl exec into the runner pod
        $innerCommand = $execPrefix . $sanitized;
        $fullCommand = sprintf(
            '%s --kubeconfig %s exec -n %s %s -- sh -c %s 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($namespace),
            escapeshellarg($runnerPod),
            escapeshellarg($innerCommand)
        );

        $output = [];
        $returnCode = 0;
        exec($fullCommand, $output, $returnCode);

        $outputText = implode("\n", $output);

        // Friendly message for OOM kills (exit code 137 = SIGKILL, usually OOM)
        if ($returnCode === 137) {
            $outputText .= "\n\n⚠️  command terminated with exit code 137 (out of memory). Try a simpler command.";
        }

        return [
            'output' => $outputText,
            'exit_code' => $returnCode,
        ];
    }


    /**
     * Apply YAML content from the code editor to the user's vcluster.
     * Routes through the host cluster: kubectl exec with stdin piping.
     */
    public function applyYaml(LabSession $session, string $yamlContent): array
    {
        // Validate it looks like YAML (basic check)
        if (empty(trim($yamlContent))) {
            return [
                'output' => "Error: Empty YAML content. Write your manifest in the editor first.",
                'exit_code' => 1,
            ];
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');
        $namespace = $session->host_namespace;
        $runnerPod = 'kubectl-runner';

        // Route through host cluster → exec into vcluster → kubectl apply -f -
        $command = sprintf(
            '%s --kubeconfig %s exec -i -n %s %s -- kubectl apply -f - 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($namespace),
            escapeshellarg($runnerPod)
        );

        // Use proc_open to pipe YAML via stdin
        $descriptors = [
            0 => ['pipe', 'r'], // stdin
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w'], // stderr
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            return [
                'output' => "Error: Failed to execute kubectl.",
                'exit_code' => 1,
            ];
        }

        // Write YAML to stdin
        fwrite($pipes[0], $yamlContent);
        fclose($pipes[0]);

        // Read output
        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $exitCode = proc_close($process);

        $output = trim($stdout . ($stderr ? "\n" . $stderr : ''));

        return [
            'output' => $output ?: ($exitCode === 0 ? 'Applied successfully.' : 'Apply failed.'),
            'exit_code' => $exitCode,
        ];
    }

    /**
     * Submit a problem attempt: run validation rules against the cluster.
     *
     * @return array Validation results
     */
    public function submitAttempt(Challenge $challenge, ChallengeAttempt $attempt): array
    {
        // For quiz-type problems, validation is handled differently
        if ($challenge->problem_type === 'quiz') {
            return $this->validateQuiz($challenge, $attempt);
        }

        // For cluster-based problems, check real cluster state
        if (!$attempt->lab_session_id) {
            return [
                'passed' => false,
                'results' => [],
                'score' => 0,
                'total' => 0,
                'message' => 'No active environment found.',
            ];
        }

        $session = LabSession::find($attempt->lab_session_id);
        if (!$session) {
            return [
                'passed' => false,
                'results' => [],
                'score' => 0,
                'total' => 0,
                'message' => 'Environment session expired.',
            ];
        }

        $rules = $challenge->validation_rules_json ?? [];
        if (empty($rules)) {
            // No validation rules — still mark as completed
            $attempt->update([
                'submitted_at' => now(),
                'status' => 'completed',
                'completed_at' => now(),
                'points_earned' => $challenge->points ?? 10,
            ]);

            $this->updateAcceptanceRate($challenge);
            $this->destroySessionOnCompletion($session);

            return [
                'passed' => true,
                'results' => [],
                'score' => 0,
                'total' => 0,
                'points_earned' => $challenge->points ?? 10,
                'message' => 'No automated checks for this problem.',
            ];
        }

        $namespace = $session->host_namespace;
        $runnerPod = match ($challenge->category) {
            'docker' => 'docker-runner',
            'terraform' => 'terraform-runner',
            'linux' => 'linux-runner',
            default => 'kubectl-runner',
        };

        $results = $this->validator->validate($rules, $namespace, $runnerPod);

        // Calculate points
        $pointsEarned = 0;
        if ($results['passed']) {
            $basePoints = $challenge->points ?? 10;
            $hintPenalty = ($attempt->hints_used ?? 0) * 2;
            $pointsEarned = max(1, $basePoints - $hintPenalty);
        }

        // Update attempt
        $attempt->update([
            'validation_results_json' => $results['results'],
            'submitted_at' => now(),
            'points_earned' => $pointsEarned,
            'status' => $results['passed'] ? 'completed' : 'started',
            'completed_at' => $results['passed'] ? now() : null,
        ]);

        // Update acceptance rate and cleanup on success
        if ($results['passed']) {
            $this->updateAcceptanceRate($challenge);
            $this->destroySessionOnCompletion($session);
        }

        $results['points_earned'] = $pointsEarned;

        return $results;
    }


    /**
     * Wipe all user-created resources from the vcluster's default namespace.
     * Used when switching between problems or resetting a scenario.
     */
    protected function cleanVclusterNamespace(LabSession $session): void
    {
        $kubectlPath = config('govkloud.kubectl.binary_path');
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');
        $namespace = $session->host_namespace;
        $runnerPod = 'kubectl-runner';

        $resourceTypes = 'pods,deployments,services,configmaps,secrets,ingresses,networkpolicies,jobs,cronjobs,statefulsets,daemonsets,replicasets,pvc';
        exec(sprintf(
            '%s --kubeconfig %s exec -n %s %s -- kubectl delete %s --all -n default --ignore-not-found 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($namespace),
            escapeshellarg($runnerPod),
            $resourceTypes
        ));

        // Wait for resources to terminate
        sleep(3);

        Log::info("[Problem] Cleaned vcluster namespace", ['namespace' => $namespace]);
    }

    /**
     * Reset a problem's scenario (clean + re-apply initial state).
     */
    public function resetScenario(Challenge $challenge, LabSession $session): bool
    {
        try {
            $this->cleanVclusterNamespace($session);
            $this->applyScenario($challenge, $session);
            return true;
        } catch (Exception $e) {
            Log::error("Failed to reset scenario", [
                'challenge' => $challenge->slug,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Apply scenario manifests (only once, when session first becomes ready).
     */
    public function applyScenarioIfNeeded(Challenge $challenge, LabSession $session): void
    {
        // Check if scenario was already applied (using session metadata or a simple flag)
        $meta = $session->metadata ?? [];
        if (!empty($meta['scenario_applied'])) {
            return;
        }

        try {
            $this->applyScenario($challenge, $session);
            $session->update(['metadata' => array_merge($meta, ['scenario_applied' => true])]);
        } catch (Exception $e) {
            Log::warning("Could not apply scenario on status check", [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Apply scenario manifests to the vcluster.
     */
    public function applyScenario(Challenge $challenge, LabSession $session): void
    {
        $manifests = $challenge->scenario_manifests_json;
        if (empty($manifests)) {
            Log::info("[Scenario] No manifests for {$challenge->slug} (build-type)");
            return;
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');
        $namespace = $session->host_namespace;
        $runnerPod = 'kubectl-runner';

        // Wait for kubectl inside the runner pod to be ready
        $this->waitForKubectlReady($kubectlPath, $hostKubeconfig, $namespace, $runnerPod);

        // scenario_manifests_json can be a string (raw YAML) or array of YAML strings
        $yamlContent = is_array($manifests)
            ? implode("\n---\n", $manifests)
            : $manifests;

        Log::info("[Scenario] Applying manifests for {$challenge->slug}", [
            'namespace' => $namespace,
            'yaml_length' => strlen($yamlContent),
        ]);

        // Route through host cluster → exec into runner → kubectl apply -f -
        $command = sprintf(
            '%s --kubeconfig %s exec -i -n %s %s -- kubectl apply -f - 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($namespace),
            escapeshellarg($runnerPod)
        );

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        $process = proc_open($command, $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new Exception("Failed to exec into runner pod for scenario apply");
        }

        fwrite($pipes[0], $yamlContent);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]);
        fclose($pipes[1]);

        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[2]);

        $returnCode = proc_close($process);

        Log::info("[Scenario] Apply result for {$challenge->slug}", [
            'exit_code' => $returnCode,
            'stdout' => trim($stdout),
            'stderr' => trim($stderr),
        ]);

        // Wait for the scenario state to establish
        $this->waitForScenarioReady($challenge);
    }

    /**
     * Wait for scenario resources to reach their expected (potentially broken) state.
     */
    protected function waitForScenarioReady(Challenge $challenge): void
    {
        // Give K8s a few seconds to process the manifests
        // For troubleshooting problems, we want the broken state to establish
        $waitSeconds = match ($challenge->problem_type) {
            'troubleshoot' => 10,  // Wait for error states to appear
            'scenario' => 8,
            default => 3,
        };

        sleep($waitSeconds);
    }

    /**
     * Wait until kubectl is functional inside the runner pod.
     * The pod may be "Running" but its startup command (copy + configure kubeconfig) needs time.
     */
    protected function waitForKubectlReady(string $kubectlPath, string $hostKubeconfig, string $namespace, string $runnerPod): void
    {
        $maxAttempts = 15;
        for ($i = 0; $i < $maxAttempts; $i++) {
            $output = [];
            $returnCode = 0;
            exec(sprintf(
                '%s --kubeconfig %s exec -n %s %s -- kubectl version --client --short 2>&1',
                escapeshellarg($kubectlPath),
                escapeshellarg($hostKubeconfig),
                escapeshellarg($namespace),
                escapeshellarg($runnerPod)
            ), $output, $returnCode);

            if ($returnCode === 0) {
                Log::info("[Scenario] kubectl ready in runner pod after {$i} retries");
                return;
            }

            Log::debug("[Scenario] kubectl not ready yet (attempt {$i}): " . implode(' ', $output));
            sleep(2);
        }

        Log::warning("[Scenario] kubectl never became ready after {$maxAttempts} attempts, proceeding anyway");
    }

    /**
     * Create a lab session record for a problem (provisioning is async via job).
     */
    protected function createProblemSession(User $user, Challenge $challenge): LabSession
    {
        $namespace = config('govkloud.host_k8s.namespace_prefix') . 'prob-' . $user->username;

        return LabSession::create([
            'user_id' => $user->id,
            'status' => LabSession::STATUS_PROVISIONING,
            'host_namespace' => $namespace,
            'vcluster_release_name' => 'vc-prob-' . substr(md5($user->id), 0, 8),
            'session_token' => bin2hex(random_bytes(16)),
            'expires_at' => now()->addMinutes(
                $challenge->time_limit_minutes
                    ?? config('govkloud.session.ttl_default_minutes')
            ),
        ]);
    }

    /**
     * Get the kubeconfig file path for a session's vcluster.
     */
    protected function getSessionKubeconfig(LabSession $session): ?string
    {
        $hostKubeconfig = config('govkloud.host_k8s.kubeconfig_path');
        $kubectlPath = config('govkloud.kubectl.binary_path');
        $namespace = $session->host_namespace;

        // Extract kubeconfig from the vcluster-kubeconfig secret
        $output = [];
        $returnCode = 0;
        exec(sprintf(
            '%s --kubeconfig %s get secret vcluster-kubeconfig -n %s -o jsonpath={.data.config} 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($hostKubeconfig),
            escapeshellarg($namespace)
        ), $output, $returnCode);

        if ($returnCode !== 0 || empty($output)) {
            return null;
        }

        $kubeconfigData = base64_decode(implode('', $output));
        if (empty($kubeconfigData)) {
            return null;
        }

        // Write to a temp file and return the path
        $tempPath = sys_get_temp_dir() . '/prob-kc-' . $session->id;
        file_put_contents($tempPath, $kubeconfigData);

        return $tempPath;
    }

    /**
     * Validate a quiz-type problem (multiple choice).
     */
    protected function validateQuiz(Challenge $challenge, ChallengeAttempt $attempt): array
    {
        $userAnswer = $attempt->user_files_json['quiz_answer'] ?? null;
        $options = $challenge->quiz_options_json ?? [];

        $correctIndex = null;
        foreach ($options as $i => $option) {
            if ($option['is_correct'] ?? false) {
                $correctIndex = $i;
                break;
            }
        }

        $passed = $userAnswer !== null && (int) $userAnswer === $correctIndex;

        $pointsEarned = 0;
        if ($passed) {
            $basePoints = $challenge->points ?? 5;
            $hintPenalty = ($attempt->hints_used ?? 0) * 1;
            $pointsEarned = max(1, $basePoints - $hintPenalty);
        }

        $attempt->update([
            'validation_results_json' => [['type' => 'quiz', 'passed' => $passed]],
            'submitted_at' => now(),
            'points_earned' => $pointsEarned,
            'status' => $passed ? 'completed' : 'started',
            'completed_at' => $passed ? now() : null,
        ]);

        if ($passed) {
            $this->updateAcceptanceRate($challenge);
        }

        return [
            'passed' => $passed,
            'results' => [
                [
                    'type' => 'quiz',
                    'description' => 'Answer check',
                    'passed' => $passed,
                    'message' => $passed ? '✅ Correct!' : '❌ Incorrect. Try again.',
                ],
            ],
            'score' => $passed ? 1 : 0,
            'total' => 1,
            'points_earned' => $pointsEarned,
        ];
    }

    /**
     * Recalculate and store acceptance rate for a challenge.
     */
    protected function updateAcceptanceRate(Challenge $challenge): void
    {
        $totalAttempts = $challenge->attempts()->count();
        $completedAttempts = $challenge->attempts()->where('status', 'completed')->count();

        if ($totalAttempts > 0) {
            $challenge->update([
                'acceptance_rate' => round(($completedAttempts / $totalAttempts) * 100, 2),
            ]);
        }
    }

    /**
     * Destroy the session environment after successful completion.
     * Runs asynchronously to avoid blocking the response.
     */
    protected function destroySessionOnCompletion(LabSession $session): void
    {
        try {
            $this->sessionDestroyer->destroy($session, 'completed');
            Log::info('[Problem] Session destroyed after successful completion', [
                'session_id' => $session->id,
            ]);
        } catch (Exception $e) {
            // Non-fatal: the cleanup cron will catch it
            Log::warning('[Problem] Failed to destroy session after completion', [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sanitize and validate a user command.
     * Only allows kubectl commands. Blocks dangerous operations.
     *
     * @return string|null Sanitized command args (without kubectl prefix), or null if blocked
     */
    protected function sanitizeCommand(string $command, string $category = 'kubernetes'): ?string
    {
        $command = trim($command);

        return match ($category) {
            'docker' => $this->sanitizeDockerCommand($command),
            'terraform' => $this->sanitizeTerraformCommand($command),
            'linux' => $this->sanitizeLinuxCommand($command),
            default => $this->sanitizeKubectlCommand($command),
        };
    }

    /**
     * Sanitize kubectl commands (existing behavior).
     */
    protected function sanitizeKubectlCommand(string $command): ?string
    {
        // Must start with kubectl
        if (!str_starts_with($command, 'kubectl ')) {
            // Allow shorthand without 'kubectl' prefix
            if (preg_match('/^(get|describe|logs|apply|create|delete|edit|patch|scale|rollout|label|annotate|explain|top|exec|port-forward|run|expose|set|auth|diff|wait|cp)\b/', $command)) {
                $command = 'kubectl ' . $command;
            } else {
                return null;
            }
        }

        // Strip the 'kubectl ' prefix (we add it back with the kubeconfig)
        $args = substr($command, strlen('kubectl '));

        // Block dangerous commands
        $blockedPatterns = [
            '/\bdelete\s+(namespace|ns|clusterrole|clusterrolebinding)\b/i',
            '/\bdelete\s+--all\s+--all-namespaces\b/i',
            '/--all-namespaces.*delete/i',
            '/\bexec\b.*\b(bash|sh|zsh|csh)\b/i', // Block shell access via exec
            '/\bproxy\b/i',
            '/\bport-forward\b/i',
            '/\battach\b/i',
            '/--kubeconfig/i',  // Can't override kubeconfig
            '/--context/i',     // Can't switch contexts
            '/--cluster/i',     // Can't switch clusters
            '/--server/i',      // Can't point to different servers
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $args)) {
                return null;
            }
        }

        return $args;
    }

    /**
     * Sanitize Docker commands.
     */
    protected function sanitizeDockerCommand(string $command): ?string
    {
        // Strip 'docker ' prefix if present
        if (str_starts_with($command, 'docker ')) {
            $command = $command;
        } elseif (str_starts_with($command, 'docker-compose ') || str_starts_with($command, 'docker compose ')) {
            $command = $command;
        } else {
            // Allow shorthand: build, run, ps, images, etc.
            $dockerSubcommands = ['build', 'run', 'ps', 'images', 'inspect', 'logs', 'exec',
                'stop', 'start', 'rm', 'rmi', 'pull', 'push', 'tag', 'network', 'volume',
                'container', 'image', 'system', 'info', 'version'];
            $first = explode(' ', $command)[0] ?? '';
            if (in_array($first, $dockerSubcommands)) {
                $command = 'docker ' . $command;
            } else {
                return null;
            }
        }

        // Block dangerous Docker commands
        $blockedPatterns = [
            '/--privileged/i',
            '/--pid=host/i',
            '/--network=host/i',
            '/-v\s+\/:/i',              // Block mounting host root
            '/--mount.*source=\//i',     // Block mounting host paths
            '/docker\s+swarm/i',
            '/docker\s+node/i',
            '/docker\s+service/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $command)) {
                return null;
            }
        }

        return $command;
    }

    /**
     * Sanitize Terraform commands.
     */
    protected function sanitizeTerraformCommand(string $command): ?string
    {
        // Strip 'terraform ' prefix if present
        if (str_starts_with($command, 'terraform ')) {
            $command = $command;
        } else {
            $tfSubcommands = ['init', 'plan', 'apply', 'destroy', 'validate', 'fmt',
                'show', 'state', 'output', 'refresh', 'import', 'taint', 'untaint',
                'graph', 'providers', 'version', 'force-unlock'];
            $first = explode(' ', $command)[0] ?? '';
            if (in_array($first, $tfSubcommands)) {
                $command = 'terraform ' . $command;
            } else {
                return null;
            }
        }

        // Auto-approve apply and destroy (no interactive prompts in terminal)
        if (preg_match('/terraform\s+(apply|destroy)/', $command) && !str_contains($command, '-auto-approve')) {
            $command .= ' -auto-approve';
        }

        return $command;
    }

    /**
     * Sanitize Linux shell commands.
     */
    protected function sanitizeLinuxCommand(string $command): ?string
    {
        // Allowed Linux commands
        $allowedCommands = [
            'ls', 'cat', 'echo', 'grep', 'find', 'chmod', 'chown', 'mkdir', 'rmdir',
            'cp', 'mv', 'rm', 'touch', 'head', 'tail', 'wc', 'sort', 'uniq', 'diff',
            'ps', 'kill', 'top', 'whoami', 'id', 'groups', 'uname',
            'curl', 'wget', 'ping', 'dig', 'nslookup', 'netstat', 'ss', 'ifconfig', 'ip',
            'awk', 'sed', 'tr', 'cut', 'paste', 'tee', 'xargs',
            'tar', 'gzip', 'gunzip', 'zip', 'unzip',
            'date', 'cal', 'uptime', 'free', 'df', 'du',
            'bash', 'sh', 'pwd', 'cd', 'env', 'export', 'set', 'unset', 'alias',
            'history', 'which', 'type', 'file', 'stat', 'realpath', 'dirname', 'basename',
            'crontab', 'systemctl', 'service', 'journalctl',
            'useradd', 'usermod', 'groupadd', 'passwd',
            'ssh-keygen', 'man', 'help', 'clear',
        ];

        // Get the first word (command name)
        $parts = preg_split('/\s+/', $command, 2);
        $firstCmd = $parts[0] ?? '';

        // Allow piped commands: check first command only
        if (!in_array($firstCmd, $allowedCommands)) {
            return null;
        }

        // Block dangerous patterns
        $blockedPatterns = [
            '/\brm\s+-rf\s+\/\s*$/i',    // Block rm -rf /
            '/>\s*\/etc\//i',              // Block writing to /etc
            '/>\s*\/usr\//i',              // Block writing to /usr
            '/\bdd\b/i',                   // Block dd
            '/\bmkfs\b/i',                // Block filesystem creation
            '/\bmount\b/i',               // Block mount
            '/\bumount\b/i',              // Block umount
            '/\bshutdown\b/i',
            '/\breboot\b/i',
            '/\binit\b/i',
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $command)) {
                return null;
            }
        }

        return $command;
    }
}
