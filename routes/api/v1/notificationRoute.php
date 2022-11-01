<?php
use App\Http\Controllers\Api\NotificationController;

Route::middleware('auth:api')->group(function () {
     Route::patch('/', [
        NotificationController::class,
        'getNotifications',
    ]);
});