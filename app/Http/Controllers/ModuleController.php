<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a list of published modules/courses
     */
    public function index(Request $request)
    {
        $query = Module::published()
            ->ordered()
            ->with(['lessons', 'labs']);

        // Filter by technology if provided
        if ($request->has('tech') && $request->tech) {
            $tech = $request->tech;
            $query->whereHas('lessons', function ($q) use ($tech) {
                $q->where('subcategory', $tech);
            });
        }

        $modules = $query->get();

        // Get all technologies for the filter dropdown
        $technologies = \App\Models\Lesson::query()
            ->whereNotNull('subcategory')
            ->where('subcategory', '!=', '')
            ->selectRaw('subcategory')
            ->groupBy('subcategory')
            ->orderBy('subcategory')
            ->get();

        return view('courses.index', compact('modules', 'technologies'));
    }

    /**
     * Display a single module with its lessons and labs
     */
    public function show(string $slug)
    {
        $module = Module::where('slug', $slug)
            ->published()
            ->with([
                'lessons' => fn($q) => $q->published()->ordered(),
                'labs' => fn($q) => $q->published(),
            ])
            ->firstOrFail();

        return view('modules.show', compact('module'));
    }

    /**
     * API: List all published modules
     */
    public function apiIndex()
    {
        $modules = Module::published()
            ->ordered()
            ->withCount(['lessons', 'labs'])
            ->get();

        return response()->json([
            'data' => $modules,
        ]);
    }

    /**
     * API: Get single module details
     */
    public function apiShow(string $slug)
    {
        $module = Module::where('slug', $slug)
            ->published()
            ->with([
                'lessons' => fn($q) => $q->published()->ordered(),
                'labs' => fn($q) => $q->published(),
            ])
            ->firstOrFail();

        return response()->json([
            'data' => $module,
        ]);
    }
}
