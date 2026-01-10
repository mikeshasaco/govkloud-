<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\LabSessionController;
use App\Http\Controllers\ModuleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public endpoints
Route::get('/modules', [ModuleController::class, 'apiIndex']);
Route::get('/modules/{slug}', [ModuleController::class, 'apiShow']);
Route::get('/labs/{slug}', [LabController::class, 'apiShow']);

// Protected endpoints - use web middleware for session-based auth (same domain SPA)
Route::middleware(['web', 'auth'])->group(function () {
    // Get current user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Lab session management
    Route::post('/labs/{slug}/start', [LabSessionController::class, 'start']);
    Route::get('/lab-sessions/{id}', [LabSessionController::class, 'show']);
    Route::post('/lab-sessions/{id}/stop', [LabSessionController::class, 'stop']);
    Route::post('/lab-sessions/{id}/heartbeat', [LabSessionController::class, 'heartbeat']);
});
