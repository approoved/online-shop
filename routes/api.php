<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\GetRoleListController;
use App\Http\Controllers\ProductFieldController;
use App\Http\Controllers\ProductFilterController;
use App\Http\Controllers\ProductFieldGroupController;
use App\Http\Controllers\ProductFilterValueController;
use App\Http\Controllers\ProductFilterConfigurationController;

Route::post('/users', [UserController::class, 'store']);

Route::controller(AuthController::class)
    ->prefix('/users/me')
    ->group(function () {
        Route::patch('/verification', 'verify');
        Route::post('/token', 'login');
    });

Route::middleware('auth:api')
    ->group(function () {
        Route::delete('/users/me/token', [AuthController::class, 'logout']);

        Route::get('/roles', GetRoleListController::class);

        Route::controller(UserController::class)
            ->prefix('/users')
            ->group(function () {
                Route::get('', 'index');
                Route::get('/{userId}', 'show');
                Route::patch('/{user}', 'update');
                Route::delete('/{user}', 'destroy');
            });

        Route::controller(CategoryController::class)
            ->prefix('/categories')
            ->group(function () {
                Route::get('', 'index');
                Route::post('', 'store');
                Route::get('/{categoryId}', 'show');
                Route::patch('/{category}', 'update');
                Route::delete('/{category}', 'destroy');
            });

        Route::controller(ProductFieldGroupController::class)
            ->prefix('/product-field-groups')
            ->group(function () {
                Route::post('', 'store');
                Route::get('', 'index');
                Route::get('/{groupId}', 'show');
                Route::delete('/{group}', 'destroy');
            });

        Route::controller(ProductFieldController::class)
            ->group(function () {
                Route::post('/product-field-groups/{group}/product-fields', 'store');
                Route::get('/product-fields', 'index');
                Route::get('/product-fields/{fieldId}', 'show');
                Route::delete('/product-fields/{field}', 'destroy');
            });

        Route::controller(ProductController::class)
            ->group(function () {
                Route::get('/products', 'index');
                Route::post('/categories/{category}/products', 'store');
                Route::get('/products/{productId}', 'show');
                Route::patch('/products/{product}', 'update');
                Route::delete('/products/{product}', 'destroy');
            });

        Route::controller(ProductFilterConfigurationController::class)
            ->group(function () {
                Route::get('/categories/{category}/product-fields', 'getFieldList');
                Route::get('/product-fields/{field}/filter-types', 'getFilterTypeList');
            });

        Route::controller(ProductFilterController::class)
            ->group(function () {
                Route::get('/categories/{category}/filters', 'index');
                Route::post('categories/{category}/filters', 'store');
                Route::get('/filters/{filterId}', 'show');
                Route::patch('filters/{filter}', 'update');
                Route::delete('/filters/{filter}', 'destroy');
            });

        Route::controller(ProductFilterValueController::class)
            ->group(function () {
                Route::get('/filters/{filter}/values', 'index');
                Route::post('/filters/{filter}/values', 'store');
                Route::get('/filter-values/{valueId}', 'show');
                Route::patch('/filter-values/{value}', 'update');
                Route::delete('/filter-values/{value}', 'destroy');
            });
    });
