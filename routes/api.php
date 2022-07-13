<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
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

        Route::controller(UserController::class)
            ->group(function () {
                Route::get('/users', 'index');
                Route::get('/users/{user}', 'show');
                Route::patch('/users/{user}', 'update');
                Route::delete('/users/{user}', 'destroy');
            });

        Route::controller(CategoryController::class)
            ->group( function () {
                Route::get('/categories', 'index');
                Route::get('/categories/{category}', 'show');
                Route::post('/categories/{parent?}', 'store');
                Route::patch('/categories/{category}', 'update');
                Route::delete('/categories/{category}', 'destroy');
            });

        Route::controller(ProductController::class)
            ->group(function () {
                Route::get('/products', 'index');
                Route::post('/products', 'store');
                Route::get('/products/{product}', 'show');
                Route::patch('/products/{product}', 'update');
                Route::delete('/products/{product}', 'destroy');
            });
    });


