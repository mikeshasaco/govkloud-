<?php

namespace App\Jobs;

use App\Models\LabSession;
use App\Services\LabRuntime\SessionProvisioner;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProvisionProblemSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying (exponential: 15s, 30s, 60s)
     */
    public array $backoff = [15, 30, 60];

    /**
     * Max execution time per attempt (10 min).
     */
    public int $timeout = 600;

    public function __construct(
        public string $sessionId
    ) {
    }

    public function uniqueId(): string
    {
        return $this->sessionId;
    }

    public function handle(SessionProvisioner $provisioner): void
    {
        $session = LabSession::find($this->sessionId);

        if (!$session) {
            Log::error("ProvisionProblemSessionJob: Session not found", [
                'session_id' => $this->sessionId,
            ]);
            return;
        }

        if ($session->status !== LabSession::STATUS_PROVISIONING) {
            Log::warning("ProvisionProblemSessionJob: Not in provisioning status, skipping", [
                'session_id' => $this->sessionId,
                'current_status' => $session->status,
            ]);
            return;
        }

        if ($session->expires_at && $session->expires_at->isPast()) {
            Log::warning("ProvisionProblemSessionJob: Session expired", [
                'session_id' => $this->sessionId,
            ]);
            $session->markError('Session expired before provisioning could complete');
            return;
        }

        Log::info("ProvisionProblemSessionJob: Starting lightweight provisioning", [
            'session_id' => $this->sessionId,
            'attempt' => $this->attempts(),
        ]);

        // Use lightweight provisioner (no workbench/ingress)
        $provisioner->provisionLightweight($session);

        // Apply scenario manifests so the broken state is ready immediately
        // (e.g., CrashLooping pod, wrong selectors, etc.)
        $this->applyScenarioManifests($session);
    }

    /**
     * Apply the challenge's scenario manifests to the vcluster.
     * This creates the "broken" state for troubleshoot problems.
     */
    protected function applyScenarioManifests(LabSession $session): void
    {
        // Find the challenge linked to this session via the attempt
        $attempt = \App\Models\ChallengeAttempt::where('lab_session_id', $session->id)->first();
        if (!$attempt || !$attempt->challenge) {
            Log::warning("ProvisionProblemSessionJob: No challenge found for scenario", [
                'session_id' => $session->id,
            ]);
            return;
        }

        $challenge = $attempt->challenge;
        $manifests = $challenge->scenario_manifests_json;

        if (empty($manifests)) {
            Log::info("ProvisionProblemSessionJob: No scenario manifests for this problem (build type)", [
                'challenge' => $challenge->slug,
            ]);
            return;
        }

        Log::info("ProvisionProblemSessionJob: Applying scenario manifests", [
            'challenge' => $challenge->slug,
            'session_id' => $session->id,
        ]);

        $sessionManager = app(\App\Services\Problems\ProblemSessionManager::class);
        $sessionManager->applyScenarioIfNeeded($challenge, $session);

        Log::info("ProvisionProblemSessionJob: Scenario applied, broken state ready");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProvisionProblemSessionJob: All attempts exhausted", [
            'session_id' => $this->sessionId,
            'error' => $exception->getMessage(),
        ]);

        $session = LabSession::find($this->sessionId);
        if ($session) {
            $session->update([
                'status' => LabSession::STATUS_ERROR,
                'error_message' => 'Problem environment failed to start. Please try again.',
            ]);
        }
    }

    public function tags(): array
    {
        return ['problem-session:' . $this->sessionId];
    }
}
