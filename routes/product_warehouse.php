<?php

use App\Http\Controllers\ProductWarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('product/{code}/warehouse', [ProductWarehouseController::class, 'getProductWarehouses']);
    Route::get('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'getProductWarehouseDetail']);
    Route::put('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'editProductWarehouse']);
    Route::post('product/{code}/warehouse', [ProductWarehouseController::class, 'addProductWarehouse']);
    Route::delete('product/{code}/warehouse/{id}', [ProductWarehouseController::class, 'deleteProductWarehouse']);
});
