<?php
use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
     Route::get('/', [
        NotificationController::class,
        'getNotifications',
    ]);
});