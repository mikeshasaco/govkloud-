<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a lesson
     */
    public function show(string $moduleSlug, string $lessonSlug)
    {
        $module = Module::where('slug', $moduleSlug)
            ->published()
            ->firstOrFail();

        $lesson = Lesson::where('module_id', $module->id)
            ->where('slug', $lessonSlug)
            ->published()
            ->firstOrFail();

        // Get navigation context
        $lessons = $module->lessons()->published()->ordered()->get();
        $currentIndex = $lessons->search(fn($l) => $l->id === $lesson->id);
        $prevLesson = $currentIndex > 0 ? $lessons[$currentIndex - 1] : null;
        $nextLesson = $currentIndex < $lessons->count() - 1 ? $lessons[$currentIndex + 1] : null;

        return view('lessons.show', compact('module', 'lesson', 'lessons', 'prevLesson', 'nextLesson'));
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
            ->firstOrFail();

        return response()->json([
            'data' => $lesson,
        ]);
    }
}
