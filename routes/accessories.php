<?php

use App\Http\Controllers\AccessoryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('accessory', [AccessoryController::class, 'getAccessories']);
    Route::get('accessory/{id}', [AccessoryController::class, 'getAccessoryDetail']);
    Route::put('accessory/{id}', [AccessoryController::class, 'editAccessory']);
    Route::post('accessory', [AccessoryController::class, 'addAccessory']);
    Route::delete('accessory/{id}', [AccessoryController::class, 'deleteAccessory']);
});
