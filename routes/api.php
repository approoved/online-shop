<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/users', [UserController::class, 'store']);

Route::controller(AuthController::class)
    ->group(function () {
        Route::patch('/users/me/verification', 'verify');
        Route::post('/users/me/token', 'login');
    });

Route::middleware('auth:api')
    ->group(function () {
        Route::delete('/users/me/token', [AuthController::class, 'logout']);
    });
