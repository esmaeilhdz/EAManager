<?php

use App\Http\Controllers\ProductAccessoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-product|edit-product|view-product']], function () {
        Route::get('product/{code}/accessory', [ProductAccessoryController::class, 'getProductAccessories']);
        Route::get('product/{code}/accessory/{id}', [ProductAccessoryController::class, 'getProductAccessoryDetail']);
    });
    Route::group(['middleware' => ['permission:admin-product|edit-product']], function () {
        Route::put('product/{code}/accessory/{id}', [ProductAccessoryController::class, 'editProductAccessory']);
    });
    Route::group(['middleware' => ['permission:admin-product']], function () {
        Route::post('product/{code}/accessory', [ProductAccessoryController::class, 'addProductAccessory']);
        Route::delete('product/{code}/accessory/{id}', [ProductAccessoryController::class, 'deleteProductAccessory']);
    });
});
