<?php

namespace App\Services\LabRuntime;

use App\Models\LabSession;
use App\Services\K8s\K8sClient;
use App\Services\K8s\HelmClient;
use Illuminate\Support\Facades\Log;
use Exception;

class SessionDestroyer
{
    public function __construct(
        protected K8sClient $k8sClient,
        protected HelmClient $helmClient,
    ) {
    }

    /**
     * Destroy a lab session environment
     */
    public function destroy(LabSession $session, string $reason = 'manual'): bool
    {
        $namespace = $session->host_namespace;

        Log::info("Destroying session", [
            'session_id' => $session->id,
            'namespace' => $namespace,
            'reason' => $reason,
        ]);

        try {
            // Delete the entire namespace (this removes everything)
            if (!$this->k8sClient->deleteNamespace($namespace)) {
                Log::warning("Namespace deletion may have failed", [
                    'namespace' => $namespace,
                ]);
            }

            // Update session status based on reason
            $newStatus = match ($reason) {
                'ttl', 'idle' => LabSession::STATUS_EXPIRED,
                'manual' => LabSession::STATUS_DESTROYED,
                default => LabSession::STATUS_DESTROYED,
            };

            $session->update([
                'status' => $newStatus,
            ]);

            Log::info("Session destroyed successfully", [
                'session_id' => $session->id,
                'new_status' => $newStatus,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error("Session destruction failed", [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);

            // Still mark as destroyed to prevent re-attempts
            $session->update([
                'status' => LabSession::STATUS_DESTROYED,
                'error_message' => "Destruction error: " . $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Force cleanup a session (for error recovery)
     */
    public function forceCleanup(LabSession $session): bool
    {
        try {
            $this->k8sClient->deleteNamespace($session->host_namespace);
            return true;
        } catch (Exception $e) {
            Log::error("Force cleanup failed", [
                'session_id' => $session->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
