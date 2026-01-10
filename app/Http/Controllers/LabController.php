<?php

namespace App\Http\Controllers;

use App\Models\Lab;
use Illuminate\Http\Request;

class LabController extends Controller
{
    /**
     * Display lab details with start button
     */
    public function show(string $slug)
    {
        $lab = Lab::where('slug', $slug)
            ->published()
            ->with(['module', 'steps'])
            ->firstOrFail();

        return view('labs.show', compact('lab'));
    }

    /**
     * API: Get lab details
     */
    public function apiShow(string $slug)
    {
        $lab = Lab::where('slug', $slug)
            ->published()
            ->with('steps')
            ->firstOrFail();

        return response()->json([
            'data' => $lab,
        ]);
    }
}
