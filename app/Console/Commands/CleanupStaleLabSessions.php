<?php

namespace App\Console\Commands;

use App\Jobs\ProvisionLabSessionJob;
use App\Models\LabSession;
use Illuminate\Console\Command;

class CleanupStaleLabSessions extends Command
{
    protected $signature = 'labs:cleanup-stale';
    protected $description = 'Detect and recover lab sessions stuck in provisioning after container restarts';

    public function handle(): int
    {
        // Sessions stuck in "provisioning" for more than 5 minutes — retry them
        $staleProvisioningSessions = LabSession::where('status', LabSession::STATUS_PROVISIONING)
            ->where('created_at', '<', now()->subMinutes(5))
            ->where('created_at', '>', now()->subMinutes(30)) // Don't retry ancient ones
            ->get();

        foreach ($staleProvisioningSessions as $session) {
            $this->info("Retrying provisioning for session {$session->id} (age: {$session->created_at->diffForHumans()})");

            // Re-dispatch the provisioning job
            ProvisionLabSessionJob::dispatch($session->id);
        }

        // Sessions stuck for more than 30 minutes — mark as error (give up)
        $abandonedSessions = LabSession::where('status', LabSession::STATUS_PROVISIONING)
            ->where('created_at', '<', now()->subMinutes(30))
            ->get();

        foreach ($abandonedSessions as $session) {
            $this->warn("Marking session {$session->id} as error (stuck for {$session->created_at->diffForHumans()})");
            $session->update([
                'status' => LabSession::STATUS_ERROR,
                'error_message' => 'Provisioning timed out — please try starting a new lab.',
            ]);
        }

        // Sessions running past their expiry — mark as destroyed
        $expiredSessions = LabSession::whereIn('status', [
                LabSession::STATUS_RUNNING,
                LabSession::STATUS_VALIDATING,
            ])
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredSessions as $session) {
            $this->info("Cleaning up expired session {$session->id}");
            $session->update(['status' => LabSession::STATUS_DESTROYED]);
        }

        $total = $staleProvisioningSessions->count() + $abandonedSessions->count() + $expiredSessions->count();

        if ($total > 0) {
            $this->info("Cleaned up {$total} stale sessions.");
        }

        return self::SUCCESS;
    }
}
