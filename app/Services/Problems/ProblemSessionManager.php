<?php

namespace App\Services\Problems;

use App\Models\Challenge;
use App\Models\ChallengeAttempt;
use App\Models\LabSession;
use App\Models\User;
use App\Services\K8s\K8sClient;
use App\Services\K8s\HelmClient;
use App\Services\K8s\IngressUrlBuilder;
use App\Services\LabRuntime\SessionProvisioner;
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
        if (!$challenge->requires_cluster) {
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

            // Provision a new lightweight session
            $session = $this->provisionLightweightSession($user, $challenge);

            // Apply scenario manifests
            $this->applyScenario($challenge, $session);

            // Link attempt to session
            $attempt->update(['lab_session_id' => $session->id]);

            return [
                'status' => 'ready',
                'session_id' => $session->id,
                'message' => 'Environment provisioned and scenario loaded.',
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
     *
     * @return array ['output' => string, 'exit_code' => int]
     */
    public function executeCommand(LabSession $session, string $command): array
    {
        // Security: validate and sanitize the command
        $sanitized = $this->sanitizeCommand($command);

        if ($sanitized === null) {
            return [
                'output' => "Error: Command not allowed. Only kubectl commands are permitted.",
                'exit_code' => 1,
            ];
        }

        // Get the vcluster kubeconfig path
        $kubeconfigPath = $this->getSessionKubeconfig($session);

        if (!$kubeconfigPath) {
            return [
                'output' => "Error: Session environment not found. Try restarting the problem.",
                'exit_code' => 1,
            ];
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');
        $fullCommand = sprintf(
            '%s --kubeconfig %s %s 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($kubeconfigPath),
            $sanitized
        );

        $output = [];
        $returnCode = 0;
        exec($fullCommand, $output, $returnCode);

        return [
            'output' => implode("\n", $output),
            'exit_code' => $returnCode,
        ];
    }

    /**
     * Apply YAML content from the code editor to the user's vcluster.
     * Pipes the YAML via stdin to `kubectl apply -f -`.
     */
    public function applyYaml(LabSession $session, string $yamlContent): array
    {
        $kubeconfigPath = $this->getSessionKubeconfig($session);

        if (!$kubeconfigPath) {
            return [
                'output' => "Error: Session environment not found. Try restarting the problem.",
                'exit_code' => 1,
            ];
        }

        // Validate it looks like YAML (basic check)
        if (empty(trim($yamlContent))) {
            return [
                'output' => "Error: Empty YAML content. Write your manifest in the editor first.",
                'exit_code' => 1,
            ];
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');
        $command = sprintf(
            '%s --kubeconfig %s apply -f - 2>&1',
            escapeshellarg($kubectlPath),
            escapeshellarg($kubeconfigPath)
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
            // No validation rules = manual review or legacy problem
            return [
                'passed' => true,
                'results' => [],
                'score' => 0,
                'total' => 0,
                'message' => 'No automated checks for this problem.',
            ];
        }

        $kubeconfigPath = $this->getSessionKubeconfig($session);
        if (!$kubeconfigPath) {
            return [
                'passed' => false,
                'results' => [],
                'score' => 0,
                'total' => 0,
                'message' => 'Could not connect to environment.',
            ];
        }

        $results = $this->validator->validate($rules, $kubeconfigPath);

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

        // Update acceptance rate
        if ($results['passed']) {
            $this->updateAcceptanceRate($challenge);
        }

        $results['points_earned'] = $pointsEarned;

        return $results;
    }

    /**
     * Reset a problem's scenario (re-apply initial state).
     */
    public function resetScenario(Challenge $challenge, LabSession $session): bool
    {
        try {
            // Delete existing resources in the default namespace
            $kubeconfigPath = $this->getSessionKubeconfig($session);
            if (!$kubeconfigPath) {
                return false;
            }

            $kubectlPath = config('govkloud.kubectl.binary_path');

            // Delete all user-created resources in default namespace
            $resourceTypes = 'pods,deployments,services,configmaps,secrets,ingresses,networkpolicies,jobs,cronjobs,statefulsets,daemonsets,replicasets,pvc';
            exec(sprintf(
                '%s --kubeconfig %s delete %s --all -n default --ignore-not-found 2>&1',
                escapeshellarg($kubectlPath),
                escapeshellarg($kubeconfigPath),
                $resourceTypes
            ));

            // Wait a moment for cleanup
            sleep(2);

            // Re-apply scenario
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
     * Apply scenario manifests to the vcluster.
     */
    protected function applyScenario(Challenge $challenge, LabSession $session): void
    {
        $manifests = $challenge->scenario_manifests_json;
        if (empty($manifests)) {
            return;
        }

        $kubeconfigPath = $this->getSessionKubeconfig($session);
        if (!$kubeconfigPath) {
            throw new Exception("Cannot apply scenario: kubeconfig not found for session {$session->id}");
        }

        $kubectlPath = config('govkloud.kubectl.binary_path');

        // scenario_manifests_json can be a string (raw YAML) or array of YAML strings
        $yamlContent = is_array($manifests)
            ? implode("\n---\n", $manifests)
            : $manifests;

        $tempFile = tempnam(sys_get_temp_dir(), 'scenario_');
        file_put_contents($tempFile, $yamlContent);

        try {
            $output = [];
            $returnCode = 0;
            exec(sprintf(
                '%s --kubeconfig %s apply -f %s 2>&1',
                escapeshellarg($kubectlPath),
                escapeshellarg($kubeconfigPath),
                escapeshellarg($tempFile)
            ), $output, $returnCode);

            if ($returnCode !== 0) {
                $outputStr = implode("\n", $output);
                Log::warning("Scenario apply had issues", [
                    'challenge' => $challenge->slug,
                    'output' => $outputStr,
                ]);
            }

            Log::info("Scenario applied for problem", [
                'challenge' => $challenge->slug,
                'session_id' => $session->id,
            ]);

            // Wait for the scenario state to establish
            $this->waitForScenarioReady($challenge, $kubeconfigPath);

        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Wait for scenario resources to reach their expected (potentially broken) state.
     */
    protected function waitForScenarioReady(Challenge $challenge, string $kubeconfigPath): void
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
     * Provision a lightweight lab session (no code-server workbench, no DinD).
     */
    protected function provisionLightweightSession(User $user, Challenge $challenge): LabSession
    {
        $namespace = config('govkloud.host_k8s.namespace_prefix') . 'prob-' . $user->username;

        $session = LabSession::create([
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

        // Use the lightweight provisioner (namespace → vcluster → kubeconfig only)
        // No workbench or ingress needed for problems
        $success = $this->sessionProvisioner->provisionLightweight($session);

        if (!$success) {
            throw new Exception("Failed to provision problem environment");
        }

        return $session;
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
     * Sanitize and validate a user command.
     * Only allows kubectl commands. Blocks dangerous operations.
     *
     * @return string|null Sanitized command args (without kubectl prefix), or null if blocked
     */
    protected function sanitizeCommand(string $command): ?string
    {
        $command = trim($command);

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
}
