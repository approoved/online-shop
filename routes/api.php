<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GetRoleListController;
use App\Http\Controllers\ProductFilterController;
use App\Http\Controllers\ProductFilterValueController;
use App\Http\Controllers\ProductFilterConfigurationController;

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

        Route::controller(ProductFilterConfigurationController::class)
            ->group(function () {
                Route::get('/{category}/fields', 'getFieldList');
                Route::get('/{category}/filter-types', 'getFilterTypeList');
            });

        Route::controller(ProductFilterController::class)
            ->group(function () {
                Route::get('{category}/filters', 'index');
                Route::post('{category}/filters', 'store');
                Route::get('/filters/{filter}', 'show');
                Route::patch('filters/{filter}', 'update');
                Route::delete('/filters/{filter}', 'destroy');
            });

        Route::controller(ProductFilterValueController::class)
            ->group(function () {
                Route::get('/{filter}/values', 'index');
                Route::post('/{filter}/values', 'store');
                Route::get('/values/{value}', 'show');
                Route::patch('/values/{value}', 'update');
                Route::delete('/values/{value}', 'destroy');
            });
    });

