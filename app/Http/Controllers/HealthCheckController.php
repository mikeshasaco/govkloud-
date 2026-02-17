<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthCheckController extends Controller
{
    public function check(): JsonResponse
    {
        $checks = [];

        // Redis check
        try {
            Redis::ping();
            $pendingJobs = Redis::llen('queues:default');
            $checks['redis'] = ['status' => 'up', 'pending_jobs' => $pendingJobs];
        } catch (\Throwable $e) {
            $checks['redis'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = ['status' => 'up'];
        } catch (\Throwable $e) {
            $checks['database'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        // Supervisor process check
        $checks = array_merge($checks, $this->checkSupervisor());

        // Overall status
        $allUp = collect($checks)->every(fn($c) => ($c['status'] ?? '') === 'up' || ($c['status'] ?? '') === 'running');
        $anyDown = collect($checks)->contains(fn($c) => ($c['status'] ?? '') === 'down' || ($c['status'] ?? '') === 'stopped');

        return response()->json([
            'status' => $anyDown ? 'unhealthy' : ($allUp ? 'healthy' : 'degraded'),
            'checks' => $checks,
            'timestamp' => now()->toISOString(),
        ], $anyDown ? 503 : 200);
    }

    private function checkSupervisor(): array
    {
        $checks = [];

        try {
            $output = [];
            $exitCode = 0;
            exec('supervisorctl status 2>&1', $output, $exitCode);

            foreach ($output as $line) {
                // Format: "process-name   STATE   pid ..." 
                if (preg_match('/^(\S+)\s+(RUNNING|STOPPED|STARTING|FATAL|EXITED|BACKOFF)\b/', $line, $m)) {
                    $name = $m[1];
                    $state = strtolower($m[2]);
                    $status = in_array($state, ['running', 'starting']) ? 'running' : 'stopped';
                    $checks[$name] = ['status' => $status, 'state' => $state];
                }
            }
        } catch (\Throwable $e) {
            $checks['supervisor'] = ['status' => 'down', 'error' => $e->getMessage()];
        }

        return $checks;
    }
}
