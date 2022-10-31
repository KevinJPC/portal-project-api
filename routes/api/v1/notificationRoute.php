<?php
use App\Http\Controllers\Api\NotificationController;

Route::middleware('auth:api')->group(function () {
     Route::patch('/', [
        NotificationController::class,
        'createNotification',
    ]);
    Route::patch('/notification', [
        NotificationController::class,
        'getNotification',
    ]);
    Route::patch('/delate/notification', [
        NotificationController::class,
        'delateNotification',
    ]);
});