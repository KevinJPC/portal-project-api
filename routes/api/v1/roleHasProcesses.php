<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleshasProcessesController;

//Note:
/* Route::middleware(['auth:api', 'restrictToAdmin'])
 * or
 * Route::middleware(['auth:api', 'restrictToUser']) */

Route::middleware('auth:api')->group(function () {
    Route::post('/register', [
        RoleshasProcessesController::class,
        'createRolehasProcesses',
    ]);
    Route::get('/allRoles', [
        RoleshasProcessesController::class,
        'allRolesHasProcesses',
    ]);

    //Route::post('/', [RolesHasProcess::class, 'createRolehasProcesses']);
    //Route::post('/', [RolesHasProcess::class, 'createRolehasProcesses']);
});
