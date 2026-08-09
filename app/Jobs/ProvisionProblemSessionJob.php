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
