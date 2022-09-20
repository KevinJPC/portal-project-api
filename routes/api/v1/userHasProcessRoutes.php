<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserHasProcessController;

Route::middleware('auth:api')->group(function () {
    Route::post('/{process:id}', [
        UserHasProcessController::class,
        'startProcess',
    ]);
    Route::get('/insiders', [
        UserHasProcessController::class,
        'getUserProcesses',
    ]);
});
