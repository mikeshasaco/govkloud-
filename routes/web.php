<?php

use App\Http\Controllers\LabSessionController;
use App\Http\Controllers\LessonController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home - Landing Page
Route::get('/', function () {
    $modules = \App\Models\Module::published()->ordered()->get();
    $subcategories = \App\Models\Lesson::selectRaw('subcategory, COUNT(*) as count')
        ->whereNotNull('subcategory')
        ->where('is_published', true)
        ->groupBy('subcategory')
        ->orderBy('subcategory')
        ->get();
    return view('landing', compact('modules', 'subcategories'));
})->name('home');

// Account Settings (replaces dashboard)
Route::middleware(['auth'])->group(function () {
    Route::get('/account/settings', function () {
        return view('account.settings');
    })->name('account.settings');

    // My Courses (saved modules)
    Route::get('/my-courses', function () {
        $savedModules = Auth::user()->savedModules()->with('lessons')->get();
        return view('my-courses', compact('savedModules'));
    })->name('my-courses');

    // Save/Unsave module API
    Route::post('/modules/{module}/save', function (\App\Models\Module $module) {
        $saved = Auth::user()->toggleSaveModule($module);
        return response()->json(['saved' => $saved]);
    })->name('modules.save');

    // Redirect old dashboard to account settings
    Route::get('/dashboard', function () {
        return redirect()->route('account.settings');
    })->name('dashboard');
});

// Profile management (Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/show', [ProfileController::class, 'edit'])->name('profile.show');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Course routes (public)
Route::get('/courses', [ModuleController::class, 'index'])->name('courses.index');
Route::get('/courses/{slug}', [ModuleController::class, 'show'])->name('courses.show');

// Redirect old /modules URLs to /courses
Route::get('/modules', fn() => redirect()->route('courses.index'));
Route::get('/modules/{slug}', fn($slug) => redirect()->route('courses.show', $slug));

// Subscription / Billing routes
Route::get('/pricing', [SubscriptionController::class, 'index'])->name('pricing');
Route::post('/subscribe/{plan}/{interval}', [SubscriptionController::class, 'checkout'])
    ->middleware('auth')
    ->name('subscribe');
Route::get('/subscription/success', [SubscriptionController::class, 'success'])
    ->middleware('auth')
    ->name('subscription.success');
Route::get('/billing', [SubscriptionController::class, 'portal'])
    ->middleware('auth')
    ->name('billing');

// Lesson routes (auth required for lab-enabled lessons)
Route::get('/courses/{module}/lessons/{lesson}', [LessonController::class, 'show'])
    ->middleware('auth')
    ->name('lessons.show');

// Mark lesson as complete (for lessons without quizzes)
Route::post('/lessons/{lesson}/complete', function (\App\Models\Lesson $lesson) {
    // Only allow marking complete if lesson has no quiz
    if ($lesson->hasQuiz()) {
        return response()->json(['error' => 'This lesson requires passing the quiz'], 400);
    }

    $progress = Auth::user()->completeLesson($lesson);
    return response()->json([
        'completed' => true,
        'completed_at' => $progress->completed_at->toISOString()
    ]);
})->middleware('auth')->name('lessons.complete');

// Mark lesson as complete via quiz pass
Route::post('/lessons/{lesson}/complete-quiz', function (\App\Models\Lesson $lesson) {
    $score = request()->input('score', 0);

    // Only allow if quiz passed (70%+)
    if ($score < 70) {
        return response()->json(['error' => 'Quiz not passed'], 400);
    }

    $progress = Auth::user()->completeLesson($lesson, $score);
    return response()->json([
        'completed' => true,
        'score' => $score,
        'completed_at' => $progress->completed_at->toISOString()
    ]);
})->middleware('auth')->name('lessons.complete-quiz');

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
