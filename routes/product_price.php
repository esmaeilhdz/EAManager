<?php

use App\Http\Controllers\ProductPriceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-product|edit-product|view-product']], function () {
        Route::get('product/{code}/price', [ProductPriceController::class, 'getProductPrices']);
        Route::get('product/{code}/price/{id}', [ProductPriceController::class, 'getProductPriceDetail']);
    });
    Route::group(['middleware' => ['permission:admin-product|edit-product']], function () {
        Route::put('product/{code}/price/{id}', [ProductPriceController::class, 'editProductPrice']);
    });
    Route::group(['middleware' => ['permission:admin-product']], function () {
        Route::post('product/{code}/price', [ProductPriceController::class, 'addProductPrice']);
        Route::delete('product/{code}/price/{id}', [ProductPriceController::class, 'deleteProductPrice']);
    });
});
