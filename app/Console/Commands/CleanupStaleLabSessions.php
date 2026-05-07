<?php

namespace App\Console\Commands;

use App\Jobs\DestroyLabSessionJob;
use App\Jobs\ProvisionLabSessionJob;
use App\Models\LabSession;
use App\Services\K8s\HelmClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupStaleLabSessions extends Command
{
    protected $signature = 'labs:cleanup-stale';
    protected $description = 'Detect and recover lab sessions stuck in provisioning, and clean up expired K8s resources';

    public function handle(HelmClient $helmClient): int
    {
        // 1. Sessions stuck in "provisioning" for more than 2 minutes — retry them
        $staleProvisioningSessions = LabSession::where('status', LabSession::STATUS_PROVISIONING)
            ->where('created_at', '<', now()->subMinutes(2))
            ->where('created_at', '>', now()->subMinutes(10)) // Don't retry ancient ones
            ->get();

        foreach ($staleProvisioningSessions as $session) {
            $this->info("Retrying provisioning for session {$session->id} (age: {$session->created_at->diffForHumans()})");
            ProvisionLabSessionJob::dispatch($session->id);
        }

        // 2. Sessions stuck for more than 10 minutes — mark as error (give up)
        $abandonedSessions = LabSession::where('status', LabSession::STATUS_PROVISIONING)
            ->where('created_at', '<', now()->subMinutes(10))
            ->get();

        foreach ($abandonedSessions as $session) {
            $this->warn("Marking session {$session->id} as error (stuck for {$session->created_at->diffForHumans()})");
            $session->update([
                'status' => LabSession::STATUS_ERROR,
                'error_message' => 'Provisioning timed out — please try starting a new lab.',
            ]);
        }

        // 3. Sessions running past their expiry — destroy them properly
        $expiredSessions = LabSession::whereIn('status', [
                LabSession::STATUS_RUNNING,
                LabSession::STATUS_VALIDATING,
            ])
            ->where('expires_at', '<', now())
            ->get();

        foreach ($expiredSessions as $session) {
            $this->info("Destroying expired session {$session->id}");
            DestroyLabSessionJob::dispatch($session->id, 'ttl');
        }

        // 4. Clean up orphaned K8s workbench releases for sessions already marked destroyed
        //    (catches cases where DB was updated but K8s resources survived a container restart)
        $recentlyDestroyed = LabSession::whereIn('status', [
                LabSession::STATUS_DESTROYED,
                LabSession::STATUS_EXPIRED,
                LabSession::STATUS_ERROR,
            ])
            ->whereNotNull('workbench_release_name')
            ->where('updated_at', '>', now()->subHours(24)) // Only check recent ones
            ->get();

        foreach ($recentlyDestroyed as $session) {
            if (!$session->workbench_release_name || !$session->host_namespace) {
                continue;
            }

            try {
                // Check if the Helm release still exists
                if ($helmClient->releaseExists($session->workbench_release_name, $session->host_namespace)) {
                    $this->warn("Cleaning up orphaned workbench: {$session->workbench_release_name} in {$session->host_namespace}");
                    $helmClient->uninstall($session->workbench_release_name, $session->host_namespace);
                    Log::info("Cleaned up orphaned workbench release", [
                        'release' => $session->workbench_release_name,
                        'namespace' => $session->host_namespace,
                        'session_id' => $session->id,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning("Failed to clean up orphaned workbench", [
                    'release' => $session->workbench_release_name,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $total = $staleProvisioningSessions->count() + $abandonedSessions->count() + $expiredSessions->count();

        if ($total > 0) {
            $this->info("Processed {$total} stale sessions.");
        }

        return self::SUCCESS;
    }
}
