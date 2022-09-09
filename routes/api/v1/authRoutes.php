<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Routing\Route;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);