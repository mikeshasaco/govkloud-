<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    /**
     * Display a list of published modules
     */
    public function index()
    {
        $modules = Module::published()
            ->ordered()
            ->withCount(['lessons', 'labs'])
            ->get();

        return view('modules.index', compact('modules'));
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
