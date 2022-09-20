<?php

use App\Http\Controllers\Api\PasswordResetController;

Route::post('/forgot', [PasswordResetController::class, 'forgotPassword']);

Route::post('/reset', [PasswordResetController::class, 'resetPassword']);
