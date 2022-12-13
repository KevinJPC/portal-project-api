<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::patch('/', [UserController::class, 'updateUser']);
    Route::patch('/update-password', [UserController::class, 'updatePassword']);
    Route::get('/{user:id}', [UserController::class, 'getUserById']);
});
