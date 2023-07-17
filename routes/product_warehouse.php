<?php

use App\Http\Controllers\ProductWarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-product|edit-product|view-product']], function () {
        Route::get('product/{code}/warehouse', [ProductWarehouseController::class, 'getProductWarehouses']);
        Route::get('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'getProductWarehouseDetail']);
        Route::get('place/{id}/product', [ProductWarehouseController::class, 'getProductsOfPlace']);
    });
    Route::group(['middleware' => ['permission:admin-product|edit-product']], function () {
        Route::put('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'editProductWarehouse']);
    });
    Route::group(['middleware' => ['permission:admin-product']], function () {
        Route::post('product/{code}/warehouse', [ProductWarehouseController::class, 'addProductWarehouse']);
        Route::delete('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'deleteProductWarehouse']);
    });
});
