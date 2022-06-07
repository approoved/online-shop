<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;

Route::post('/users', [UserController::class, 'store']);

Route::controller(AuthController::class)->group(function () {
    Route::post('/verify/{token}', 'verify')
        ->name('verify.email');
    Route::post('/login', 'login');
});

Route::middleware('auth:api')->group(function () {

    Route::get('/logout', [AuthController::class, 'logout']);
});
