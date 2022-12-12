<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProcessController;

Route::middleware(['auth:sanctum', 'restrictTo:user'])->get('/visibles', [
    ProcessController::class,
    'getVisiblesProcesses',
]);

Route::middleware(['auth:sanctum', 'restrictTo:user'])->get(
    '/visibles/{process:name}',
    [ProcessController::class, 'getSearchVisiblesProcesses'],
);

Route::middleware(['auth:sanctum', 'restrictTo:admin'])->get('/inactives', [
    ProcessController::class,
    'getInactiveProcesses',
]);

Route::middleware(['auth:sanctum', 'restrictTo:admin'])->get('/actives', [
    ProcessController::class,
    'getActiveProcesses',
]);

Route::middleware(['auth:sanctum', 'restrictTo:admin'])->group(function () {
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
});
