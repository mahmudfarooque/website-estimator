<?php

// START: Our temporary route for debugging the API call
Route::get('/test-api', function() {
    $domain = 'google.com'; // We use a domain we know is taken
    $apiKey = config('services.godaddy.key');
    $apiSecret = config('services.godaddy.secret');

    if (!$apiKey || !$apiSecret) {
        return 'API keys are not set in your .env file!';
    }

    try {
        $response = Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "sso-key {$apiKey}:{$apiSecret}"
        ])->get("https://api.godaddy.com/v1/domains/available", [
            'domain' => $domain
        ]);

        // Dump the entire response and stop execution
        dd($response->json(), $response->status(), $response->headers());

    } catch (\Exception $e) {
        // If the call itself fails, dump the error
        dd($e->getMessage());
    }
});
// END: Our temporary route for debugging the API call


use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\AiDesignController;
use App\Http\Controllers\ProjectUploadController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Project & Estimator Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}/summary', [ProjectController::class, 'summary'])->name('projects.summary');

    // Domain Check Route
    Route::post('/domain-check', [ProjectController::class, 'checkDomain'])->name('domain.check');

    // Design / Upload Routes
    Route::get('/projects/{project}/design', [ProjectController::class, 'showDesignForm'])->name('projects.design');
    Route::post('/projects/{project}/upload', [ProjectUploadController::class, 'store'])->name('projects.uploads.store');
    Route::delete('/uploads/{upload}', [ProjectUploadController::class, 'destroy'])->name('projects.uploads.destroy');

    // AI Generation Route (we will keep this for later)
    Route::post('/projects/{project}/design/generate', [AiDesignController::class, 'generate'])->name('projects.design.generate');

    
    Route::post('/projects/{project}/submit-order', [ProjectController::class, 'submitOrder'])->name('projects.order.submit');
    
});

require __DIR__.'/auth.php';