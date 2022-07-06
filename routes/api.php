<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GetRoleListController;

Route::post('/users', [UserController::class, 'store']);

Route::controller(AuthController::class)
    ->group(function () {
        Route::patch('/users/me/verification', 'verify');
        Route::post('/users/me/token', 'login');
    });

Route::middleware('auth:api')
    ->group(function () {
        Route::delete('/users/me/token', [AuthController::class, 'logout']);

        Route::get('/roles', GetRoleListController::class);

        Route::controller(CategoryController::class)
            ->group( function () {
                Route::post('/categories/{parent?}', 'store');
                Route::get('/categories', 'index');
                Route::get('/categories/{category}', 'show');
                Route::patch('/categories/{category}', 'update');
                Route::delete('/categories/{category}', 'destroy');
            });
    });


