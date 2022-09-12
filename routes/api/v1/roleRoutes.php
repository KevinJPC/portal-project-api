<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;

/* A group of routes that are protected by the `auth:sanctum` middleware. */

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/', [RoleController::class, 'createRole']);
    Route::get('/', [RoleController::class, 'getAllRoles']);
    Route::get('/{role:id}', [RoleController::class, 'getRole']);
    Route::patch('/{role:id}/inactivate', [RoleController::class, 'inactivateRole']);
    Route::patch('/{role:id}', [RoleController::class, 'update']);
    Route::patch('/{role:id}/activate', [RoleController::class, 'activateRole']);
});
