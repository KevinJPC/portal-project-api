<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProcessController;

Route::middleware('auth:sanctum')->group(function(){

    Route::post('/register', [ProcessController::class, 'registerProcess']);
    Route::patch('/{process:id}', [ProcessController::class, 'updateProcess']);
    Route::patch('/{process:id}/inactivate', [ProcessController::class, 'inactivateProcess']);
    Route::patch('/{process:id}/activate', [ProcessController::class, 'activateProcess']);
    
    Route::get('/actives', [ProcessController::class, 'getActiveProcesses']);
    Route::get('/inactives', [ProcessController::class, 'getInactiveProcesses']);
    
});