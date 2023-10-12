<?php

use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-warehouse|edit-warehouse|view-warehouse']], function () {
        Route::get('warehouse', [WarehouseController::class, 'getWarehouses']);
        Route::get('warehouse/{code}', [WarehouseController::class, 'getWarehouseDetail']);
    });
    Route::group(['middleware' => ['permission:admin-warehouse|edit-warehouse']], function () {
        Route::put('warehouse/{code}', [WarehouseController::class, 'editWarehouse']);
    });
    Route::group(['middleware' => ['permission:admin-warehouse']], function () {
        Route::post('warehouse', [WarehouseController::class, 'addWarehouse']);
        Route::delete('warehouse/{code}', [WarehouseController::class, 'deleteWarehouse']);
    });
});
