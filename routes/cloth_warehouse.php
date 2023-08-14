<?php

use App\Http\Controllers\ClothBuyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth|view-cloth']], function () {
        Route::get('cloth/{code}/warehouse', [\App\Http\Controllers\ClothWarehouseController::class, 'getClothWarehouses']);
    });
});
