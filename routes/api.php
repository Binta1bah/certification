<?php

use App\Http\Controllers\api\RoleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::group(['middleware' => 'auth'], function () {
    Route::get('/logout', [UserController::class, 'logout']);
    Route::get('/profil', [UserController::class, 'profil']);
    Route::get('/refresh', [UserController::class, 'refresh']);
    Route::put('/update', [UserController::class, 'update']);
});

Route::group(['middleware' => ['auth:api', 'admin']], function () {
    Route::get('/usersBloques', [UserController::class, 'usersBloques']);
    Route::put('/bloquer/{user}', [UserController::class, 'bloquerUser']);
    Route::put('/debloquer/{user}', [UserController::class, 'debloquerUser']);
    Route::apiResource('roles', RoleController::class);
});

Route::group(['middleware' => ['auth:api', 'user']], function () {
});


Route::post('/inscription', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::get('/users', [UserController::class, 'index']);
