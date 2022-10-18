<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleshasProcessesController;
Route::middleware('auth:api')->group(function () {

    Route::post('/register', [RoleshasProcessesController::class, 'createRolehasProcesses']);
    Route::get('/allRoles', [RoleshasProcessesController::class, 'allRolesHasProcesses']);
    Route::get('/{process:id}/getRole', [RoleshasProcessesController::class, 'getRoleHasProcesses']);
    Route::get('/modify', [RoleshasProcessesController::class, 'modifyRolehasProcesses']);
    //Route::post('/', [RoleshasProcessesController::class, 'createRolehasProcesses']);

});