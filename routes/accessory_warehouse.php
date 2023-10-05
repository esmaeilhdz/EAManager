<?php

use App\Http\Controllers\AccessoryWarehouseController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-accessory|edit-accessory|view-accessory']], function () {
        Route::get('accessory/{accessory_id}/warehouse', [AccessoryWarehouseController::class, 'getAccessoryWarehouses']);
    });
});
