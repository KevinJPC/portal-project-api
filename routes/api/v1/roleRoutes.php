<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;

/* A group of routes that are protected by the `auth:sanctum` middleware. */

Route::middleware('auth:api')->group(function () {
    Route::post('/', [RoleController::class, 'createRole']);
    Route::get('/inactives', [RoleController::class, 'getInactiveRoles']);
    Route::get('/actives', [RoleController::class, 'getActiveRoles']);
    Route::get('/{role:id}', [RoleController::class, 'getRoleById']);
    Route::get('/{role:name}', [RoleController::class, 'searchRole']);
    Route::patch('/{role:id}/inactivate', [
        RoleController::class,
        'inactivateRole',
    ]);
    Route::patch('/{role:id}', [RoleController::class, 'updateRole']);
    Route::patch('/{role:id}/activate', [
        RoleController::class,
        'activateRole',
    ]);
});