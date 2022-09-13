<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('/roles')->group(base_path('routes/api/v1/roleRoutes.php'));

Route::prefix('/roleHasProcesses')->group(base_path('routes/api/v1/roleHasProcesses.php'));

Route::prefix('/auth')->group(base_path('routes/api/v1/authRoutes.php'));

Route::prefix('/user')->group(base_path('routes/api/v1/userRoutes.php'));
