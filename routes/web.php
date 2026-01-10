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

// Module routes
Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
Route::get('/modules/{slug}', [ModuleController::class, 'show'])->name('modules.show');

// Lesson routes
Route::get('/modules/{module}/lessons/{lesson}', [LessonController::class, 'show'])
    ->name('lessons.show');

// Lab routes
Route::get('/labs/{slug}', [LabController::class, 'show'])->name('labs.show');

// Lab session runtime (protected)
Route::middleware('auth')->group(function () {
    Route::get('/lab-sessions/{id}', [LabSessionController::class, 'runtime'])
        ->name('lab-sessions.runtime');
});

require __DIR__ . '/auth.php';
