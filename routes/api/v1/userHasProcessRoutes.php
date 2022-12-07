<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserHasProcessController;

Route::middleware(['auth:sanctum', 'restrictTo:user'])->group(function () {
    Route::post('/{process:id}/start', [
        UserHasProcessController::class,
        'startProcess',
    ]);
    Route::get('/insiders', [
        UserHasProcessController::class,
        'getUserProcesses',
    ]);
    Route::middleware(['ensureIsOwnerUser'])->get('/{usershasprocess:id}', [
        UserHasProcessController::class,
        'getUserProcessById',
    ]);
    Route::middleware(['ensureIsOwnerUser'])->get(
        '/{usershasprocess:id}/enabled-activity/form',
        [UserHasProcessController::class, 'getUserProcessEnabledActivityForm'],
    );
    Route::middleware(['ensureIsOwnerUser'])->post(
        '/{usershasprocess:id}/enabled-activity/form',
        [UserHasProcessController::class, 'saveUserProcessEnabledActivityForm'],
    );
});
