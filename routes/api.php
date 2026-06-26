<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestAttemptController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // Test Attempt Routes
    Route::prefix('assessments')->group(function () {
        // Get assigned assessments
        Route::get('/', [TestAttemptController::class, 'getAssignedAssessments']);

        // Get assessment detail (cover page)
        Route::get('/{assessmentId}', [TestAttemptController::class, 'getAssessmentDetail']);

        // ── NEW: Get attempt status (attempts used, remaining, etc) ──
        Route::get('/{assessmentId}/attempt-status', [TestAttemptController::class, 'getAttemptStatus']);
    });

    Route::prefix('attempts')->group(function () {
        // Get user's attempts
        Route::get('/my-attempts', [TestAttemptController::class, 'myAttempts']);

        // Start new attempt
        Route::post('/start', [TestAttemptController::class, 'startAttempt']);

        // Get specific attempt
        Route::get('/{attemptId}', [TestAttemptController::class, 'getAttempt']);

        // Save response
        Route::post('/{attemptId}/response', [TestAttemptController::class, 'saveResponse']);

        // Submit attempt
        Route::post('/{attemptId}/submit', [TestAttemptController::class, 'submitAttempt']);

        // Get attempt result
        Route::get('/{attemptId}/result', [TestAttemptController::class, 'getAttemptResult']);
    });
});
