<?php

namespace App\Jobs;

use App\Models\LabSession;
use App\Services\LabRuntime\SessionDestroyer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DestroyLabSessionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * Timeout in seconds
     */
    public int $timeout = 300; // 5 minutes

    public function __construct(
        public string $sessionId,
        public string $reason = 'manual'
    ) {
    }

    public function handle(SessionDestroyer $destroyer): void
    {
        $session = LabSession::find($this->sessionId);

        if (!$session) {
            Log::warning("DestroyLabSessionJob: Session not found", [
                'session_id' => $this->sessionId,
            ]);
            return;
        }

        // Skip if already destroyed
        if ($session->status === LabSession::STATUS_DESTROYED) {
            Log::info("DestroyLabSessionJob: Session already destroyed", [
                'session_id' => $this->sessionId,
            ]);
            return;
        }

        Log::info("DestroyLabSessionJob: Starting destruction", [
            'session_id' => $this->sessionId,
            'reason' => $this->reason,
        ]);

        $destroyer->destroy($session, $this->reason);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("DestroyLabSessionJob: Job failed", [
            'session_id' => $this->sessionId,
            'error' => $exception->getMessage(),
        ]);
    }
}
