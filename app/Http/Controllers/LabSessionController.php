<?php

namespace App\Http\Controllers;

use App\Jobs\DestroyLabSessionJob;
use App\Jobs\ProvisionLabSessionJob;
use App\Models\Lab;
use App\Models\LabSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LabSessionController extends Controller
{
    /**
     * Start a new lab session
     * POST /api/labs/{lab:slug}/start
     */
    public function start(Request $request, string $slug)
    {
        $lab = Lab::where('slug', $slug)->published()->firstOrFail();
        $user = $request->user();

        // Check for existing active sessions (MVP: max 1)
        $activeCount = LabSession::where('user_id', $user->id)
            ->active()
            ->count();

        $maxConcurrent = config('govkloud.session.max_concurrent_sessions');

        if ($activeCount >= $maxConcurrent) {
            return response()->json([
                'error' => 'You already have an active lab session. Please stop it before starting a new one.',
                'active_sessions' => $activeCount,
            ], 409);
        }

        // Generate unique identifiers
        $shortId = strtolower(Str::random(8));
        $namespacePrefix = config('govkloud.host_k8s.namespace_prefix');

        // Create session record
        $session = LabSession::create([
            'user_id' => $user->id,
            'lab_id' => $lab->id,
            'status' => LabSession::STATUS_PROVISIONING,
            'host_namespace' => $namespacePrefix . $shortId,
            'vcluster_release_name' => 'vc-' . $shortId,
            'session_token' => Str::random(32),
            'expires_at' => now()->addMinutes($lab->ttl_minutes),
        ]);

        // Dispatch provisioning job
        ProvisionLabSessionJob::dispatch($session->id);

        return response()->json([
            'session_id' => $session->id,
            'status' => $session->status,
            'expires_at' => $session->expires_at->toIso8601String(),
        ], 201);
    }

    /**
     * Get session status
     * GET /api/lab-sessions/{id}
     */
    public function show(Request $request, string $id)
    {
        $session = LabSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'session_id' => $session->id,
            'status' => $session->status,
            'code_url' => $session->code_url,
            'expires_at' => $session->expires_at->toIso8601String(),
            'error_message' => $session->error_message,
        ]);
    }

    /**
     * Stop a lab session
     * POST /api/lab-sessions/{id}/stop
     */
    public function stop(Request $request, string $id)
    {
        $session = LabSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        if ($session->status === LabSession::STATUS_DESTROYED) {
            return response()->json([
                'message' => 'Session already destroyed',
            ]);
        }

        // Dispatch destruction job
        DestroyLabSessionJob::dispatch($session->id, 'manual');

        return response()->json([
            'message' => 'Session stop initiated',
            'session_id' => $session->id,
        ]);
    }

    /**
     * Record heartbeat for idle timeout tracking
     * POST /api/lab-sessions/{id}/heartbeat
     */
    public function heartbeat(Request $request, string $id)
    {
        $session = LabSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->whereIn('status', [LabSession::STATUS_RUNNING, LabSession::STATUS_VALIDATING])
            ->firstOrFail();

        $session->recordActivity();

        return response()->json([
            'message' => 'Heartbeat recorded',
            'last_activity_at' => $session->last_activity_at->toIso8601String(),
        ]);
    }

    /**
     * Start a new lab session from module page (web - redirects to runtime)
     * POST /modules/{module}/start-lab
     */
    public function startFromModule(Request $request, string $moduleSlug)
    {
        $module = \App\Models\Module::where('slug', $moduleSlug)->published()->firstOrFail();
        $user = $request->user();

        // Get the first lab associated with this module
        $lab = $module->labs()->published()->first();

        if (!$lab) {
            return redirect()->route('modules.show', $moduleSlug)
                ->with('error', 'No lab available for this module.');
        }

        // Check for existing active session for this lab
        $existingSession = LabSession::where('user_id', $user->id)
            ->where('lab_id', $lab->id)
            ->whereIn('status', [LabSession::STATUS_PROVISIONING, LabSession::STATUS_RUNNING])
            ->first();

        if ($existingSession) {
            return redirect()->route('lab-sessions.runtime', $existingSession->id);
        }

        // Check max concurrent sessions
        $activeCount = LabSession::where('user_id', $user->id)->active()->count();
        $maxConcurrent = config('govkloud.session.max_concurrent_sessions');

        if ($activeCount >= $maxConcurrent) {
            return redirect()->route('modules.show', $moduleSlug)
                ->with('error', 'You already have an active lab session. Please stop it before starting a new one.');
        }

        // Create new session
        $shortId = strtolower(Str::random(8));
        $namespacePrefix = config('govkloud.host_k8s.namespace_prefix');

        $session = LabSession::create([
            'user_id' => $user->id,
            'lab_id' => $lab->id,
            'status' => LabSession::STATUS_PROVISIONING,
            'host_namespace' => $namespacePrefix . $shortId,
            'vcluster_release_name' => 'vc-' . $shortId,
            'session_token' => Str::random(32),
            'expires_at' => now()->addMinutes($lab->ttl_minutes),
        ]);

        // Dispatch provisioning job
        ProvisionLabSessionJob::dispatch($session->id);

        return redirect()->route('lab-sessions.runtime', $session->id);
    }

    /**
     * Display lab runtime page (web view) with module lessons on left
     */
    public function runtime(Request $request, string $id)
    {
        $session = LabSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->with([
                'lab.module.lessons' => function ($q) {
                    $q->published()->ordered();
                },
                'lab.steps'
            ])
            ->firstOrFail();

        // Get the module and its lessons for the left panel
        $module = $session->lab->module;
        $lessons = $module ? $module->lessons()->published()->ordered()->get() : collect();

        return view('labs.runtime', compact('session', 'module', 'lessons'));
    }

    /**
     * API: Get session status for AJAX polling
     * GET /api/lab-sessions/{id}/status
     */
    public function apiStatus(Request $request, string $id)
    {
        $session = LabSession::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        return response()->json([
            'data' => [
                'id' => $session->id,
                'status' => $session->status,
                'code_url' => $session->code_url,
                'session_token' => $session->session_token,
                'expires_at' => $session->expires_at?->toIso8601String(),
                'error_message' => $session->error_message,
            ]
        ]);
    }
}
