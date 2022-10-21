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
    Route::get('/{usershasprocess:id}/activity/{activity_se_oid}', [
        UserHasProcessController::class,
        'getUserProcessActivityByOid',
    ]);
});
