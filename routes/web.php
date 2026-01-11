<?php

use App\Http\Controllers\LabController;
use App\Http\Controllers\LabSessionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home - redirect to modules
Route::get('/', function () {
    return redirect()->route('modules.index');
});

// Dashboard (Breeze)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profile management (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Module routes (public)
Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
Route::get('/modules/{slug}', [ModuleController::class, 'show'])->name('modules.show');

// Lesson routes (auth required for lab-enabled lessons)
Route::get('/modules/{module}/lessons/{lesson}', [LessonController::class, 'show'])
    ->middleware('auth')
    ->name('lessons.show');

// Lab routes
Route::get('/labs/{slug}', [LabController::class, 'show'])->name('labs.show');

// Lab session routes (protected)
Route::middleware('auth')->group(function () {
    Route::get('/lab-sessions/{id}', [LabSessionController::class, 'runtime'])
        ->name('lab-sessions.runtime');
    Route::post('/lab-sessions', [LabSessionController::class, 'start'])
        ->name('lab-sessions.start');
    Route::post('/modules/{module}/start-lab', [LabSessionController::class, 'startFromModule'])
        ->name('modules.start-lab');
});

// API routes for AJAX calls
Route::prefix('api')->middleware('auth')->group(function () {
    Route::get('/lab-sessions/{id}/status', [LabSessionController::class, 'apiStatus'])
        ->name('api.lab-sessions.status');
    Route::post('/lab-sessions/{id}/stop', [LabSessionController::class, 'stop'])
        ->name('api.lab-sessions.stop');
    Route::post('/lab-sessions/{id}/heartbeat', [LabSessionController::class, 'heartbeat'])
        ->name('api.lab-sessions.heartbeat');
});

require __DIR__ . '/auth.php';
