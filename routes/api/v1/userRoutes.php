<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UpdatePasswordController;
use App\Http\Controllers\Api\EditUserController;

Route::post('/updatepassword', [UpdatePasswordController::class, 'updatePassword']);
Route::post('/edituser', [EditUserController::class, 'editUser']);
