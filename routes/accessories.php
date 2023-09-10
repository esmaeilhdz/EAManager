<?php

use App\Http\Controllers\AccessoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-accessory|edit-accessory|view-accessory']], function () {
        Route::get('accessory', [AccessoryController::class, 'getAccessories']);
        Route::get('accessory/{id}', [AccessoryController::class, 'getAccessoryDetail']);
        Route::get('accessory_combo', [AccessoryController::class, 'getAccessoryCombo']);
    });
    Route::group(['middleware' => ['permission:admin-accessory|edit-accessory']], function () {
        Route::put('accessory/{id}', [AccessoryController::class, 'editAccessory']);
        Route::patch('accessory/{id}', [AccessoryController::class, 'changeStatusAccessory']);
    });
    Route::group(['middleware' => ['permission:admin-accessory']], function () {
        Route::post('accessory', [AccessoryController::class, 'addAccessory']);
        Route::delete('accessory/{id}', [AccessoryController::class, 'deleteAccessory']);
    });
});
