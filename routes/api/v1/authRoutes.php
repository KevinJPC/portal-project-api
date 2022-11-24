<?php

use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    //Note: Changes route to /user instead /reconnect
    Route::post('/reconnect', [AuthController::class, 'reconnect']);

    Route::post('/logout', [AuthController::class, 'logout']);
});
