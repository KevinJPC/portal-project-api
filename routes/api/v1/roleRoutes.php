<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\Controller;

//Route::post('/', [RoleController::class , 'store']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/', [RoleController::class, 'store']);
    Route::get('/', [RoleController::class, 'index']);
    Route::get('/{role:id}', [RoleController::class, 'show']);
    Route::delete('/{role:id}', [RoleController::class, 'destroy']);
    Route::patch('/{role:id}', [RoleController::class, 'update']);
});
