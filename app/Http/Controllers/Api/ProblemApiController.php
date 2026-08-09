<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Challenge;
use App\Services\Problems\ProblemSessionManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API endpoints for real-cluster problem interactions.
 *
 * POST /api/problems/{slug}/start   → Provision environment + apply scenario
 * POST /api/problems/{slug}/exec    → Run kubectl command in user's vcluster
 * POST /api/problems/{slug}/submit  → Validate cluster state (auto-grade)
 * POST /api/problems/{slug}/reset   → Reset scenario to initial state
 */
class ProblemApiController extends Controller
{
    public function __construct(
        protected ProblemSessionManager $sessionManager,
    ) {}

    /**
     * Start a problem session.
     * Provisions or reuses a vcluster and applies the scenario manifests.
     *
     * POST /api/problems/{slug}/start
     */
    public function start(string $slug): JsonResponse
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();

        // Check subscription for gated problems
        if ($challenge->requiresSubscription() && !$user->isSubscribed() && !$user->onTrial()) {
            return response()->json([
                'status' => 'error',
                'message' => 'This problem requires a subscription.',
            ], 403);
        }

        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        $result = $this->sessionManager->startSession($user, $challenge, $attempt);

        return response()->json($result);
    }

    /**
     * Check the status of a problem session (polled by frontend during provisioning).
     *
     * GET /api/problems/{slug}/status
     */
    public function status(string $slug): JsonResponse
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        if (!$attempt->lab_session_id) {
            return response()->json([
                'status' => 'not_started',
                'message' => 'No session started.',
            ]);
        }

        $session = $attempt->labSession;
        if (!$session) {
            return response()->json([
                'status' => 'not_started',
                'message' => 'Session not found.',
            ]);
        }

        $response = [
            'status' => $session->status,
            'session_id' => $session->id,
        ];

        if ($session->isRunning()) {
            // Session is ready — apply scenario if not done yet
            $this->sessionManager->applyScenarioIfNeeded($challenge, $session);
            $response['message'] = 'Environment ready.';
        } elseif ($session->status === LabSession::STATUS_ERROR) {
            $response['message'] = $session->error_message ?? 'Environment failed to start.';
        } else {
            $response['message'] = 'Provisioning...';
        }

        return response()->json($response);
    }

    /**
     * Execute a kubectl command in the user's problem environment.
     *
     * POST /api/problems/{slug}/exec
     * Body: { "command": "kubectl get pods" }
     */
    public function exec(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'command' => 'required|string|max:2000',
        ]);

        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        if (!$attempt->lab_session_id) {
            return response()->json([
                'output' => "Error: No active environment. Click 'Start' to begin.",
                'exit_code' => 1,
            ]);
        }

        $session = $attempt->labSession;
        if (!$session || !$session->isRunning()) {
            return response()->json([
                'output' => "Error: Environment has expired. Click 'Start' to restart.",
                'exit_code' => 1,
            ]);
        }

        // Track the command
        $commands = $attempt->commands_executed ?? [];
        $commands[] = [
            'command' => $request->input('command'),
            'timestamp' => now()->toISOString(),
        ];
        $attempt->update(['commands_executed' => $commands]);

        // Execute against the real vcluster
        $result = $this->sessionManager->executeCommand($session, $request->input('command'));

        return response()->json($result);
    }

    /**
     * Apply YAML from the code editor to the user's cluster.
     *
     * POST /api/problems/{slug}/apply
     * Body: { "yaml": "apiVersion: v1\nkind: Pod..." }
     */
    public function apply(Request $request, string $slug): JsonResponse
    {
        $request->validate([
            'yaml' => 'required|string|max:50000',
        ]);

        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        if (!$attempt->lab_session_id) {
            return response()->json([
                'output' => "Error: No active environment. Click 'Start' to begin.",
                'exit_code' => 1,
            ]);
        }

        $session = $attempt->labSession;
        if (!$session || !$session->isRunning()) {
            return response()->json([
                'output' => "Error: Environment has expired. Click 'Start' to restart.",
                'exit_code' => 1,
            ]);
        }

        $result = $this->sessionManager->applyYaml($session, $request->input('yaml'));

        return response()->json($result);
    }

    /**
     * Submit the problem for auto-grading.
     * Validates the cluster state against the challenge's validation rules.
     *
     * POST /api/problems/{slug}/submit
     */
    public function submit(Request $request, string $slug): JsonResponse
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        // Save any final file state
        if ($request->has('files')) {
            $attempt->saveProgress(
                files: $request->input('files'),
                timeSpent: $request->input('time_spent_seconds')
            );
        }

        // For quiz problems, save the selected answer
        if ($challenge->isQuiz() && $request->has('quiz_answer')) {
            $attempt->update([
                'user_files_json' => array_merge(
                    $attempt->user_files_json ?? [],
                    ['quiz_answer' => $request->input('quiz_answer')]
                ),
            ]);
        }

        // Run validation
        $results = $this->sessionManager->submitAttempt($challenge, $attempt);

        // Include solution if passed
        if ($results['passed']) {
            $results['solution_files'] = $challenge->getSolutionFiles();
            $results['solution_explanation'] = $challenge->solution_explanation;
        }

        return response()->json($results);
    }

    /**
     * Reset the problem scenario to its initial state.
     *
     * POST /api/problems/{slug}/reset
     */
    public function reset(string $slug): JsonResponse
    {
        $challenge = Challenge::where('slug', $slug)->published()->firstOrFail();
        $user = Auth::user();
        $attempt = $user->getOrCreateChallengeAttempt($challenge);

        if (!$attempt->lab_session_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'No active environment to reset.',
            ]);
        }

        $session = $attempt->labSession;
        if (!$session || !$session->isRunning()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Environment has expired.',
            ]);
        }

        $success = $this->sessionManager->resetScenario($challenge, $session);

        return response()->json([
            'status' => $success ? 'reset' : 'error',
            'message' => $success ? 'Scenario reset to initial state.' : 'Failed to reset.',
        ]);
    }

    /**
     * Stop and tear down a problem environment.
     *
     * POST /api/problems/{slug}/stop
     */
    public function stop(string $slug): JsonResponse
    {
        $user = Auth::user();

        // Find the user's active problem session
        $session = $user->labSessions()
            ->whereIn('status', ['running', 'provisioning'])
            ->latest()
            ->first();

        if (!$session) {
            return response()->json([
                'status' => 'ok',
                'message' => 'No active session.',
            ]);
        }

        try {
            $destroyer = app(\App\Services\LabRuntime\SessionDestroyer::class);
            $destroyer->destroy($session, 'manual');
        } catch (\Exception $e) {
            // Mark as destroyed even if K8s cleanup fails
            $session->update(['status' => 'destroyed']);
        }

        return response()->json([
            'status' => 'stopped',
            'message' => 'Environment stopped.',
        ]);
    }
}
