<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProcessController;
use App\Http\Controllers\Api\SeSuiteController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/roles')->group(base_path('routes/api/v1/roleRoutes.php'));

Route::prefix('/roleHasProcesses')->group(
    base_path('routes/api/v1/roleHasProcesses.php'),
);

Route::prefix('/auth')->group(base_path('routes/api/v1/authRoutes.php'));

Route::prefix('/password')->group(
    base_path('routes/api/v1/passwordResetRoutes.php'),
);

Route::prefix('/users')->group(base_path('routes/api/v1/userRoutes.php'));

Route::prefix('/admin')->group(base_path('routes/api/v1/adminRoutes.php'));

Route::prefix('/processes')->group(
    base_path('routes/api/v1/processRoutes.php'),
);

Route::prefix('/user-has-process')->group(
    base_path('routes/api/v1/userHasProcessRoutes.php'),
);

Route::get('/sesuite/test-ws', [SeSuiteController::class, 'testWs']);
