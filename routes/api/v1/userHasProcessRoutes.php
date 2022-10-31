<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserHasProcessController;

Route::middleware('auth:api')->group(function () {
    Route::post('/{process:id}/start', [
        UserHasProcessController::class,
        'startProcess',
    ]);
    Route::get('/insiders', [
        UserHasProcessController::class,
        'getUserProcesses',
    ]);
    Route::get('/{usershasprocess:id}', [
        UserHasProcessController::class,
        'getUserProcessById',
    ]);
    Route::get('/{usershasprocess:id}/enabled-activity/form', [
        UserHasProcessController::class,
        'getUserProcessEnabledActivityForm',
    ]);
    Route::post('/{usershasprocess:id}/enabled-activity/form', [
        UserHasProcessController::class,
        'saveUserProcessEnabledActivityForm',
    ]);
});
