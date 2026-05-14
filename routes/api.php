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
        Route::post('/health-checkup/manual', [AiController::class, 'healthCheckupManual']);
        Route::post('/exercise/generate', [AiController::class, 'exerciseGenerate']);
        Route::post('/recipe/daily-menu', [AiController::class, 'recipeDailyMenu']);
        Route::post('/recipe/weekly-menu', [AiController::class, 'recipeWeeklyMenu']);
        Route::post('/chat', [AiController::class, 'chat']);
    });
});

// Health-report history — list and detail. Session-aware so the user gets
// only their own reports.
Route::middleware('api.session')->prefix('health-reports')->group(function () {
    Route::get('/', [AiController::class, 'listReports']);
    Route::get('/{id}', [AiController::class, 'showReport'])->where('id', '[0-9]+');
});

// Persisted history: chat conversations, exercise plans, meal plans. All
// scoped to the session user — anonymous requests get empty lists / 404s.
Route::middleware('api.session')->prefix('chat-conversations')->group(function () {
    Route::get('/', [AiController::class, 'listConversations']);
    Route::get('/{id}', [AiController::class, 'showConversation'])->where('id', '[0-9]+');
    Route::delete('/{id}', [AiController::class, 'deleteConversation'])->where('id', '[0-9]+');
});

Route::middleware('api.session')->prefix('exercise-plans')->group(function () {
    Route::get('/', [AiController::class, 'listExercisePlans']);
    Route::get('/{id}', [AiController::class, 'showExercisePlan'])->where('id', '[0-9]+');
});

Route::middleware('api.session')->prefix('meal-plans')->group(function () {
    Route::get('/', [AiController::class, 'listMealPlans']);
    Route::get('/active', [AiController::class, 'activeMealPlan']);
    Route::get('/{id}', [AiController::class, 'showMealPlan'])->where('id', '[0-9]+');
});

Route::middleware('api.session')->prefix('meal-likes')->group(function () {
    Route::get('/', [AiController::class, 'listLikedMeals']);
    Route::post('/', [AiController::class, 'likeMeal']);
    Route::delete('/{id}', [AiController::class, 'unlikeMeal'])->where('id', '[0-9]+');
});

// Small profile endpoint so client-side views can read the session user
// without depending on Sanctum bearer tokens.
Route::middleware('api.session')->get('/profile/me', [AiController::class, 'profileMe']);
Route::middleware('api.session')->patch('/profile/me', [AiController::class, 'updateProfile']);

// Exercise dataset images — serves the animated GIF for an exercise_id
// (or the still JPG via ?kind=jpg). No auth — these are reference images
// that can be cached aggressively on the client.
Route::get('/exercises/{id}/image', function (string $id, \Illuminate\Http\Request $request) {
    return app(AiController::class)->exerciseImage($id, $request->query('kind', 'gif'));
})->where('id', '[0-9]+');
