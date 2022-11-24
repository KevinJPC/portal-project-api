<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;

/* A group of routes that are protected by the `auth:sanctum` middleware. */

//Note: Restrict all routes to admin role, like this:
/* Route::middleware(['auth:api', 'restrictToAdmin'])
 * or
 * Route::middleware(['auth:api', 'restrictToUser']) */

Route::middleware('auth:api')->group(function () {
    Route::get('/inactives', [RoleController::class, 'getInactiveRoles']);
    Route::get('/actives', [RoleController::class, 'getActiveRoles']);
    Route::post('/', [RoleController::class, 'createRole']);
    Route::get('/{role:id}', [RoleController::class, 'getRoleById']);
    Route::patch('/{role:id}', [RoleController::class, 'updateRole']);
    Route::patch('/{role:id}/inactivate', [
        RoleController::class,
        'inactivateRole',
    ]);
    Route::patch('/{role:id}/activate', [
        RoleController::class,
        'activateRole',
    ]);
    Route::get('/search/{role:name}', [RoleController::class, 'searchRole']);
});
