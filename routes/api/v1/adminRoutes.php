<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AdminController;

Route::middleware(['auth:api', 'restrictToAdmin'])->group(function () {
    Route::get('/actives', [AdminController::class, 'getActiveAdmins']);
    Route::get('/inactives', [AdminController::class, 'getInactiveAdmins']);
    Route::post('/register', [AdminController::class, 'registerAdmin']);
    Route::patch('/{user:id}', [AdminController::class, 'updateAdmin']);
    Route::patch('/{user:id}/inactivate', [
        AdminController::class,
        'inactivateAdmin',
    ]);
    Route::patch('/{user:id}/activate', [
        AdminController::class,
        'activateAdmin',
    ]);
    Route::get('/search/{user:name}', [AdminController::class, 'searchAdmin']);
});
