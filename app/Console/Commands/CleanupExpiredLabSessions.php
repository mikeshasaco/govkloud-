<?php

namespace App\Console\Commands;

use App\Jobs\DestroyLabSessionJob;
use App\Models\LabSession;
use Illuminate\Console\Command;

class CleanupExpiredLabSessions extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'govkloud:cleanup-sessions';

    /**
     * The console command description.
     */
    protected $description = 'Cleanup expired lab sessions (TTL and idle timeout)';

    public function handle(): int
    {
        $this->info('Checking for expired lab sessions...');

        $sessions = LabSession::needsCleanup()->get();

        if ($sessions->isEmpty()) {
            $this->info('No expired sessions found.');
            return Command::SUCCESS;
        }

        $this->info("Found {$sessions->count()} expired session(s) to cleanup.");

        foreach ($sessions as $session) {
            $reason = $session->isExpiredByTtl() ? 'ttl' : 'idle';

            $this->line("  - Destroying session {$session->id} (reason: {$reason})");

            // Mark as expired before dispatching
            $session->update(['status' => LabSession::STATUS_EXPIRED]);

            DestroyLabSessionJob::dispatch($session->id, $reason);
        }

        $this->info('Cleanup jobs dispatched.');

        return Command::SUCCESS;
    }
}
