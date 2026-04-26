<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use App\Models\LabSession;
use App\Jobs\ProvisionLabSessionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LessonController extends Controller
{
    /**
     * Display a lesson (with or without associated lab)
     * GET /courses/{slug}/lessons/{lesson}
     */
    public function show(string $moduleSlug, Lesson $lesson)
    {
        $module = Module::where('slug', $moduleSlug)
            ->published()
            ->firstOrFail();

        // Ensure lesson belongs to this module
        abort_if($lesson->module_id !== $module->id, 404);
        abort_if(!$lesson->is_published, 404);

        $lesson->load('lab');

        // Get navigation context
        $lessons = $module->lessons()->published()->ordered()->get();
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        // Check if module has any labs - if so, get/create a module session
        $moduleHasLabs = $module->labs()->exists();

        if ($moduleHasLabs) {
            $session = $this->getOrCreateModuleSession($module);

            return view('lessons.show-with-lab', compact(
                'module',
                'lesson',
                'lessons',
                'prevLesson',
                'nextLesson',
                'session'
            ));
        }

        // No labs in module - show regular lesson view
        return view('lessons.show', compact('module', 'lesson', 'lessons', 'prevLesson', 'nextLesson'));
    }

    /**
     * Get existing session or create a new one for the entire module
     * All lessons in the module share the same vcluster session
     */
    protected function getOrCreateModuleSession(Module $module): LabSession
    {
        $user = Auth::user();

        // Check for existing running/provisioning session for this user and MODULE
        $session = LabSession::where('user_id', $user->id)
            ->where('module_id', $module->id)
            ->whereIn('status', [LabSession::STATUS_PROVISIONING, LabSession::STATUS_RUNNING])
            ->first();

        if ($session) {
            return $session;
        }

        // Generate short ID for namespaces and release names
        $shortId = strtolower(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8));

        // Get default TTL from first lab or use config default
        $ttlMinutes = $module->labs()->first()?->ttl_minutes ?? config('govkloud.session.ttl_minutes', 60);

        // Create new session with all required fields - linked to MODULE not lab
        $session = LabSession::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'status' => LabSession::STATUS_PROVISIONING,
            'expires_at' => now()->addMinutes($ttlMinutes),
            'host_namespace' => 'gk-sess-' . $shortId,
            'vcluster_release_name' => 'vc-' . $shortId,
            'session_token' => bin2hex(random_bytes(16)),
        ]);

        // Dispatch provisioning job
        ProvisionLabSessionJob::dispatch($session->id);

        return $session;
    }
}
