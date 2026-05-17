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

class ProvisionLabSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     * 3 attempts with exponential backoff handles transient K8s issues.
     */
    public int $tries = 3;

    /**
     * Seconds to wait before retrying (exponential: 30s, 60s, 120s)
     */
    public array $backoff = [30, 60, 120];

    /**
     * Max execution time per attempt (15 min).
     * vcluster startup can take 5-10 min.
     */
    public int $timeout = 900;

    /**
     * Prevent overlapping jobs for the same session.
     * If a job is already running for this session, skip.
     */
    public function uniqueId(): string
    {
        return $this->sessionId;
    }

    public function __construct(
        public string $sessionId
    ) {
    }

    public function handle(SessionProvisioner $provisioner): void
    {
        $session = LabSession::find($this->sessionId);

        if (!$session) {
            Log::error("ProvisionLabSessionJob: Session not found", [
                'session_id' => $this->sessionId,
            ]);
            return;
        }

        // Only provision if still in provisioning status
        if ($session->status !== LabSession::STATUS_PROVISIONING) {
            Log::warning("ProvisionLabSessionJob: Session not in provisioning status, skipping", [
                'session_id' => $this->sessionId,
                'current_status' => $session->status,
            ]);
            return;
        }

        // Check if session has expired while waiting in queue
        if ($session->expires_at && $session->expires_at->isPast()) {
            Log::warning("ProvisionLabSessionJob: Session expired before provisioning completed", [
                'session_id' => $this->sessionId,
                'expired_at' => $session->expires_at,
            ]);
            $session->markError('Session expired before provisioning could complete');
            return;
        }

        Log::info("ProvisionLabSessionJob: Starting provisioning", [
            'session_id' => $this->sessionId,
            'lab_id' => $session->lab_id,
            'attempt' => $this->attempts(),
        ]);

        $provisioner->provision($session);
    }

    /**
     * Handle a job failure after all retries are exhausted.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("ProvisionLabSessionJob: All attempts exhausted", [
            'session_id' => $this->sessionId,
            'attempts' => $this->attempts(),
            'error' => $exception->getMessage(),
        ]);

        $session = LabSession::find($this->sessionId);
        if ($session) {
            // Mark as error with a user-friendly message and auto-cleanup
            $session->update([
                'status' => LabSession::STATUS_ERROR,
                'error_message' => 'Lab environment failed to start. Please try again.',
            ]);
        }
    }

    /**
     * Determine the tags for the job (for Horizon monitoring).
     */
    public function tags(): array
    {
        return ['lab-session:' . $this->sessionId];
    }
}
