<?php

use App\Http\Controllers\api\ArticleController;
use App\Http\Controllers\api\CategorieController;
use App\Http\Controllers\api\LocaliteController;
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
    Route::get('/users', [UserController::class, 'index']);
    // Route::apiResource('categories', CategorieController::class);
    // Route pour les categories
    Route::post('categories', [CategorieController::class, 'store']);
    Route::get('categories', [CategorieController::class, 'index']);
    Route::delete('categories/{categorie}', [CategorieController::class, 'destroy']);
    Route::put('categories/{categorie}', [CategorieController::class, 'update']);
    // Route pour les localités
    Route::post('localites', [LocaliteController::class, 'store']);
    Route::get('localites', [LocaliteController::class, 'index']);
    Route::delete('localites/{localite}', [LocaliteController::class, 'destroy']);
    Route::put('localites/{localite}', [LocaliteController::class, 'update']);
    // Route pour les articles
    Route::post('articles', [ArticleController::class, 'store']);

    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
    Route::put('articles/{article}', [ArticleController::class, 'update']);
});

Route::group(['middleware' => ['auth:api', 'user']], function () {
});


Route::post('/inscription', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
