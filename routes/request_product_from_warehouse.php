<?php

use App\Http\Controllers\RequestProductWarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-request_product_from_warehouse|edit-request_product_from_warehouse|view-request_product_from_warehouse']], function () {
        Route::get('product/{code}/warehouse/{warehouse_id}/request', [RequestProductWarehouseController::class, 'getRequestProductWarehouses']);
        Route::get('product/{code}/warehouse/{warehouse_id}/request/{id}', [RequestProductWarehouseController::class, 'getRequestProductWarehouseDetail']);
    });
    Route::group(['middleware' => ['permission:admin-request_product_from_warehouse|edit-request_product_from_warehouse']], function () {
        Route::put('product/{code}/warehouse/{warehouse_id}/request/{id}', [RequestProductWarehouseController::class, 'editRequestProductWarehouse']);
        Route::patch('product/{code}/warehouse/{warehouse_id}/request/{id}', [RequestProductWarehouseController::class, 'confirmRequestProductWarehouse']);
    });
    Route::group(['middleware' => ['permission:admin-request_product_from_warehouse']], function () {
        Route::post('product/{code}/warehouse/{warehouse_id}/request', [RequestProductWarehouseController::class, 'addRequestProductWarehouse']);
        Route::delete('product/{code}/warehouse/{warehouse_id}/request/{id}', [RequestProductWarehouseController::class, 'deleteRequestProductWarehouse']);
    });
});
