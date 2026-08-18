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
        public string $sessionId,
        public string $challengeSlug = ''
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

        // Determine the challenge category to route provisioning
        $challenge = $this->challengeSlug
            ? \App\Models\Challenge::where('slug', $this->challengeSlug)->first()
            : null;
        $category = $challenge?->category ?? 'kubernetes';

        Log::info("ProvisionProblemSessionJob: Starting provisioning", [
            'session_id' => $this->sessionId,
            'challenge' => $this->challengeSlug,
            'category' => $category,
            'attempt' => $this->attempts(),
        ]);

        // Route to category-specific provisioner (Docker/Terraform/Linux skip vcluster)
        $success = match ($category) {
            'docker' => $provisioner->provisionDocker($session),
            'terraform' => $provisioner->provisionTerraform($session),
            'linux' => $provisioner->provisionLinux($session),
            default => $provisioner->provisionLightweight($session),  // kubernetes
        };

        if (!$success) {
            return;
        }

        // Apply scenario manifests (only for kubernetes problems)
        if ($category === 'kubernetes') {
            $this->applyScenarioManifests($session);
        }
    }

    /**
     * Apply the challenge's scenario manifests to the vcluster.
     * This creates the "broken" state for troubleshoot problems.
     */
    protected function applyScenarioManifests(LabSession $session): void
    {
        // Find the challenge directly by slug
        $challenge = null;

        if ($this->challengeSlug) {
            $challenge = \App\Models\Challenge::where('slug', $this->challengeSlug)->first();
        }

        // Fallback: lookup via attempt
        if (!$challenge) {
            $attempt = \App\Models\ChallengeAttempt::where('lab_session_id', $session->id)->first();
            $challenge = $attempt?->challenge;
        }

        if (!$challenge) {
            Log::warning("ProvisionProblemSessionJob: No challenge found for scenario", [
                'session_id' => $session->id,
                'slug' => $this->challengeSlug,
            ]);
            return;
        }

        $manifests = $challenge->scenario_manifests_json;

        if (empty($manifests)) {
            Log::info("ProvisionProblemSessionJob: No scenario (build-type problem)", [
                'challenge' => $challenge->slug,
            ]);
            return;
        }

        Log::info("ProvisionProblemSessionJob: Applying broken scenario", [
            'challenge' => $challenge->slug,
            'manifest_count' => is_array($manifests) ? count($manifests) : 1,
        ]);

        try {
            $sessionManager = app(\App\Services\Problems\ProblemSessionManager::class);
            $sessionManager->applyScenario($challenge, $session);

            // Mark it as applied in session metadata
            $meta = $session->metadata ?? [];
            $session->update(['metadata' => array_merge($meta, ['scenario_applied' => true])]);

            Log::info("ProvisionProblemSessionJob: Broken state ready ✓", [
                'challenge' => $challenge->slug,
            ]);
        } catch (\Exception $e) {
            Log::error("ProvisionProblemSessionJob: Failed to apply scenario", [
                'challenge' => $challenge->slug,
                'error' => $e->getMessage(),
            ]);
        }
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
