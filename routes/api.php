<?php

use App\Http\Controllers\Ai\AiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('ai')->group(function () {
    Route::get('/health', [AiController::class, 'health']);

    // Open dev/demo helpers — no auth, useful for frontend development.
    Route::get('/health-checkup/sample', [AiController::class, 'healthCheckupSample']);
    Route::get('/health-checkup/schema', [AiController::class, 'healthCheckupSchema']);
    Route::get('/health-checkup/probe', [AiController::class, 'healthCheckupProbe']);

    // Upload + exercise generate use 'api.session' so the controller can read
    // the session user (set by AuthController) and persist / personalize.
    // No CSRF — they're JSON / multipart POSTs from authenticated pages.
    Route::middleware('api.session')->group(function () {
        Route::post('/health-checkup/upload', [AiController::class, 'healthCheckupUpload']);
        Route::post('/exercise/generate', [AiController::class, 'exerciseGenerate']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/health-checkup/manual', [AiController::class, 'healthCheckupManual']);
        Route::post('/recipe/daily-menu', [AiController::class, 'recipeDailyMenu']);
    });
});

// Health-report history — list and detail. Session-aware so the user gets
// only their own reports.
Route::middleware('api.session')->prefix('health-reports')->group(function () {
    Route::get('/', [AiController::class, 'listReports']);
    Route::get('/{id}', [AiController::class, 'showReport'])->where('id', '[0-9]+');
});
