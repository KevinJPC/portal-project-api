<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::post('/updatepassword', [UserController::class, 'updatePassword']);
Route::post('/updateuser', [UserController::class, 'updateUser']);
