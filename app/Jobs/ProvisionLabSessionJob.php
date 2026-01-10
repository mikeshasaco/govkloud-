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
     * Number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * Timeout in seconds
     */
    public int $timeout = 900; // 15 minutes

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
            Log::warning("ProvisionLabSessionJob: Session not in provisioning status", [
                'session_id' => $this->sessionId,
                'current_status' => $session->status,
            ]);
            return;
        }

        Log::info("ProvisionLabSessionJob: Starting provisioning", [
            'session_id' => $this->sessionId,
            'lab_id' => $session->lab_id,
        ]);

        $provisioner->provision($session);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("ProvisionLabSessionJob: Job failed", [
            'session_id' => $this->sessionId,
            'error' => $exception->getMessage(),
        ]);

        $session = LabSession::find($this->sessionId);
        if ($session) {
            $session->markError("Provisioning job failed: " . $exception->getMessage());
        }
    }
}
