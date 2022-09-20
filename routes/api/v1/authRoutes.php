<?php

use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:api')->post('/logout', [
    AuthController::class,
    'logout',
]);

//route to test protected routes
Route::middleware('auth:api')->get('/user', function () {
    return Auth::user();
});
