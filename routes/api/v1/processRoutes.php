<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProcessController;

Route::middleware('auth:api')->get('/visibles', [ProcessController::class, 'getVisiblesProcesses']);

Route::middleware('auth:api')->group(function(){

    Route::post('/register', [ProcessController::class, 'registerProcess']);
    Route::patch('/{process:id}', [ProcessController::class, 'updateProcess']);
    Route::patch('/{process:id}/inactivate', [ProcessController::class, 'inactivateProcess']);
    Route::patch('/{process:id}/activate', [ProcessController::class, 'activateProcess']);
    
    Route::get('/{process:id}', [ProcessController::class, 'getProcessById']);
    Route::get('/actives', [ProcessController::class, 'getActiveProcesses']);
    Route::get('/inactives', [ProcessController::class, 'getInactiveProcesses']);
    
});