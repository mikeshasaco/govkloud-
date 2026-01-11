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
     */
    public function show(string $moduleSlug, string $lessonSlug)
    {
        $module = Module::where('slug', $moduleSlug)
            ->published()
            ->firstOrFail();

        $lesson = Lesson::where('module_id', $module->id)
            ->where('slug', $lessonSlug)
            ->published()
            ->with('lab')
            ->firstOrFail();

        // Get navigation context
        $lessons = $module->lessons()->published()->ordered()->get();
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        // If lesson has an associated lab, show split-screen view
        if ($lesson->hasLab()) {
            $session = $this->getOrCreateLabSession($lesson);

            return view('lessons.show-with-lab', compact(
                'module',
                'lesson',
                'lessons',
                'prevLesson',
                'nextLesson',
                'session'
            ));
        }

        // No lab - show regular lesson view
        return view('lessons.show', compact('module', 'lesson', 'lessons', 'prevLesson', 'nextLesson'));
    }

    /**
     * Get existing session or create a new one for the lab
     */
    protected function getOrCreateLabSession(Lesson $lesson): LabSession
    {
        $user = Auth::user();
        $lab = $lesson->lab;

        // Check for existing running/provisioning session for this user and lab
        $session = LabSession::where('user_id', $user->id)
            ->where('lab_id', $lab->id)
            ->whereIn('status', [LabSession::STATUS_PROVISIONING, LabSession::STATUS_RUNNING])
            ->first();

        if ($session) {
            return $session;
        }

        // Create new session
        $session = LabSession::create([
            'user_id' => $user->id,
            'lab_id' => $lab->id,
            'status' => LabSession::STATUS_PROVISIONING,
            'expires_at' => now()->addMinutes($lab->ttl_minutes),
        ]);

        // Generate namespace and session token
        $session->update([
            'host_namespace' => 'gk-sess-' . strtolower(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 8)),
            'session_token' => bin2hex(random_bytes(16)),
            'status' => LabSession::STATUS_PROVISIONING,
        ]);

        // Dispatch provisioning job
        ProvisionLabSessionJob::dispatch($session);

        return $session;
    }

    /**
     * API: Get lesson details
     */
    public function apiShow(string $moduleSlug, string $lessonSlug)
    {
        $module = Module::where('slug', $moduleSlug)
            ->published()
            ->firstOrFail();

        $lesson = Lesson::where('module_id', $module->id)
            ->where('slug', $lessonSlug)
            ->published()
            ->with('lab')
            ->firstOrFail();

        return response()->json([
            'data' => $lesson,
        ]);
    }
}
