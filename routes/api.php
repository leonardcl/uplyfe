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
    // Upload is open during development so the healthcheck page works without
    // sanctum being fully wired. Move this back into the sanctum group below
    // once auth is in place.
    Route::post('/health-checkup/upload', [AiController::class, 'healthCheckupUpload']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/health-checkup/manual', [AiController::class, 'healthCheckupManual']);
        Route::post('/exercise/generate', [AiController::class, 'exerciseGenerate']);
        Route::post('/recipe/daily-menu', [AiController::class, 'recipeDailyMenu']);
    });
});
