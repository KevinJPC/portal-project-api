<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\Controller;

//Route::post('/', [RoleController::class , 'store']);

Route::post('create/', [RoleController::class, 'store']);
Route::get('roles/', [RoleController::class, 'index']);
Route::get('show/{$id}', [RoleController::class, 'show']);
Route::delete('delete/{$id}', [RoleController::class, 'destroy']);
Route::patch('update/{$id}', [RoleController::class, 'update']);
