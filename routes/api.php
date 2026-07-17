<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestAttemptController;
use App\Http\Controllers\Api\StudentReportCardController;

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\AdminAssessmentController;
use App\Http\Controllers\Admin\AdminTestAttemptController;
use App\Http\Controllers\Api\LeaderboardController;

use App\Http\Controllers\Api\Settings\ProfileController;
use App\Http\Controllers\Api\Settings\PasswordController;
use App\Http\Controllers\Api\Settings\PreferenceController;
use App\Http\Controllers\Api\Settings\DeviceController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AdminAuthController::class, 'logout']);
        Route::get('/me', [AdminAuthController::class, 'me']);
    });
});
Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
   Route::get('/assessments', [AdminAssessmentController::class, 'index']);

         // ── Admin Assessment Preview ───────────────────────────
    Route::prefix('preview')->group(function () {
        // Get assessment detail for preview (no org/status restrictions)
        Route::get('/assessments/{assessmentId}',
            [AdminTestAttemptController::class, 'getAssessmentDetail']
        );

        // Start a new admin preview attempt
        Route::post('/attempts/start',
            [AdminTestAttemptController::class, 'startAttempt']
        );

        // Get a specific admin preview attempt
        Route::get('/attempts/{attemptId}',
            [AdminTestAttemptController::class, 'getAttempt']
        );

        // Save response during preview
        Route::post('/attempts/{attemptId}/response',
            [AdminTestAttemptController::class, 'saveResponse']
        );

        // Submit admin preview
        Route::post('/attempts/{attemptId}/submit',
            [AdminTestAttemptController::class, 'submitAttempt']
        );

        // Get preview result
        Route::get('/attempts/{attemptId}/result',
            [AdminTestAttemptController::class, 'getAttemptResult']
        );

        // Reset/delete admin previews for an assessment
        Route::delete('/assessments/{assessmentId}/reset',
            [AdminTestAttemptController::class, 'resetPreview']
        );
    });
});

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
    Route::prefix('reports')->group(function () {
          Route::get('/report-card', [StudentReportCardController::class, 'index'])
            ->name('report-card');

        Route::get('/promotion-history/{type}', [StudentReportCardController::class, 'promotionHistory'])
            ->name('promotion-history');

        Route::get('/test-attempts', [StudentReportCardController::class, 'testAttempts'])
            ->name('test-attempts');

        Route::get('/test-attempts/{id}', [StudentReportCardController::class, 'testAttemptDetail'])
            ->name('test-attempt-detail');

    });
     Route::prefix('leaderboard')->group(function () {
        Route::get('/', [LeaderboardController::class, 'index'])
            ->name('leaderboard.index');

        Route::get('/me', [LeaderboardController::class, 'me'])
            ->name('leaderboard.me');
    });
     Route::prefix('settings')->group(function () {
         Route::get('/profile', [ProfileController::class, 'show'])
        ->name('settings.profile.show');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('settings.profile.update');

    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])
        ->name('settings.profile.avatar');

    Route::put('/password', [PasswordController::class, 'update'])
        ->name('settings.password.update');

    Route::get('/notifications', [PreferenceController::class, 'notifications'])
        ->name('settings.notifications.show');

    Route::put('/notifications', [PreferenceController::class, 'updateNotifications'])
        ->name('settings.notifications.update');

    Route::get('/privacy', [PreferenceController::class, 'privacy'])
        ->name('settings.privacy.show');

    Route::put('/privacy', [PreferenceController::class, 'updatePrivacy'])
        ->name('settings.privacy.update');

    Route::get('/devices', [DeviceController::class, 'index'])
        ->name('settings.devices.index');

    Route::delete('/devices/{tokenId}', [DeviceController::class, 'revoke'])
        ->name('settings.devices.revoke');

    Route::post('/devices/revoke-all', [DeviceController::class, 'revokeAll'])
        ->name('settings.devices.revoke-all');
    });
});
