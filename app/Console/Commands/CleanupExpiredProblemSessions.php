<?php

namespace App\Console\Commands;

use App\Models\LabSession;
use App\Services\LabRuntime\SessionDestroyer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupExpiredProblemSessions extends Command
{
    protected $signature = 'problems:cleanup';
    protected $description = 'Destroy expired or orphaned problem lab sessions';

    public function handle(SessionDestroyer $destroyer): int
    {
        $this->info('Checking for expired problem sessions...');

        // Find sessions that have expired or been idle too long
        $expired = LabSession::whereIn('status', ['running', 'provisioning'])
            ->where(function ($query) {
                $query->where('expires_at', '<', now())
                    ->orWhere('last_activity_at', '<', now()->subMinutes(30));
            })
            ->where('host_namespace', 'like', '%prob-%')
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No expired sessions found.');
            return 0;
        }

        $this->info("Found {$expired->count()} expired session(s). Cleaning up...");

        foreach ($expired as $session) {
            try {
                $destroyer->destroy($session, 'expired');
                $this->info("Destroyed session {$session->id} (namespace: {$session->host_namespace})");
            } catch (\Exception $e) {
                $session->update(['status' => 'destroyed']);
                $this->warn("Failed to cleanly destroy {$session->id}: {$e->getMessage()}");
            }
        }

        $this->info('Cleanup complete.');
        return 0;
    }
}
