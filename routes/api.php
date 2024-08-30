<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/verify-code', 'verifyCode');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->apiResource('tags', TagController::class);

Route::prefix('post')->middleware('auth:sanctum')->controller(PostController::class)->group(function () {
    Route::get('/all-posts', 'index');
    Route::post('/create-post', 'store');
    Route::get('view-post/{id}', 'show');
    Route::post('update-post/{id}', 'update');
    Route::delete('delete-post/{id}', 'destroy');
    Route::get('deleted-on-trash', 'deletedPosts');
    Route::post('restore-post/{id}', 'restore');
});

Route::get('/stats', [StatsController::class, 'index'])->middleware('auth:sanctum');