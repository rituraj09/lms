<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

    Route::prefix('auth')->group(function () {
        Route::post('/login',    [AuthController::class, 'login']);
    });



Route::group(['middleware' => 'auth:sanctum'], function ($router) {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

});
