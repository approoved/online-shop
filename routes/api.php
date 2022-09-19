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
                Route::get('/users/{userId}', 'show');
                Route::patch('/users/{user}', 'update');
                Route::delete('/users/{user}', 'destroy');
            });

        Route::controller(CategoryController::class)
            ->group(function () {
                Route::get('/categories', 'index');
                Route::post('/categories', 'store');
                Route::get('/categories/{categoryId}', 'show');
                Route::patch('/categories/{category}', 'update');
                Route::delete('/categories/{category}', 'destroy');
            });

        Route::controller(ProductFieldGroupController::class)
            ->group(function () {
                Route::post('/product-field-groups', 'store');
                Route::get('/product-field-groups', 'index');
                Route::get('/product-field-groups/{groupId}', 'show');
                Route::delete('/product-field-groups/{group}', 'destroy');
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
