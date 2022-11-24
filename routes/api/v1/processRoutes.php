<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProcessController;

//Note: restrict /visibles route to user and all the others to admin role, like this:
/* Route::middleware(['auth:api', 'restrictToAdmin'])
 * or
 * Route::middleware(['auth:api', 'restrictToUser']) */

Route::middleware('auth:api')->get('/visibles', [
    ProcessController::class,
    'getVisiblesProcesses',
]);

Route::middleware('auth:api')->get('/inactives', [
    ProcessController::class,
    'getInactiveProcesses',
]);

Route::middleware('auth:api')->get('/actives', [
    ProcessController::class,
    'getActiveProcesses',
]);

Route::middleware('auth:api')->group(function () {
    Route::get('/sesuite', [ProcessController::class, 'getSeSuiteProcesses']);
    Route::post('/register', [ProcessController::class, 'registerProcess']);
    Route::get('/{process:id}', [ProcessController::class, 'getProcessById']);
    Route::patch('/{process:id}', [ProcessController::class, 'updateProcess']);
    Route::patch('/{process:id}/inactivate', [
        ProcessController::class,
        'inactivateProcess',
    ]);
    Route::patch('/{process:id}/activate', [
        ProcessController::class,
        'activateProcess',
    ]);
    Route::get('/actives', [ProcessController::class, 'getActiveProcesses']);
    Route::get('/inactives', [
        ProcessController::class,
        'getInactiveProcesses',
    ]);

    Route::get('/search/{process:name}', [
        ProcessController::class,
        'searchProcess',
    ]);
    Route::get('/visibles/{process:name}', [
        ProcessController::class,
        'getSearchVisiblesProcesses',
    ]);
});
