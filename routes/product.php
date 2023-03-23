<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-product|edit-product|view-product']], function () {
        Route::get('product', [ProductController::class, 'getProducts']);
        Route::get('product/{code}', [ProductController::class, 'getProductDetail']);
    });
    Route::group(['middleware' => ['permission:admin-product|edit-product']], function () {
        Route::put('product/{code}', [ProductController::class, 'editProduct']);
    });
    Route::group(['middleware' => ['permission:admin-product']], function () {
        Route::post('product', [ProductController::class, 'addProduct']);
        Route::delete('product/{code}', [ProductController::class, 'deleteProduct']);
    });
});
