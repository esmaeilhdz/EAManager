<?php

use App\Http\Controllers\AccessoryBuyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-accessory|edit-accessory|view-accessory']], function () {
        Route::get('accessory/{accessory_id}/buy', [AccessoryBuyController::class, 'getAccessoryBuys']);
        Route::get('accessory/{accessory_id}/buy/{id}', [AccessoryBuyController::class, 'getAccessoryBuyDetail']);
    });
    Route::group(['middleware' => ['permission:admin-accessory|edit-accessory']], function () {
        Route::put('accessory/{accessory_id}/buy/{id}', [AccessoryBuyController::class, 'editAccessoryBuy']);
    });
    Route::group(['middleware' => ['permission:admin-accessory']], function () {
        Route::post('accessory/{accessory_id}/buy', [AccessoryBuyController::class, 'addAccessoryBuy']);
        Route::delete('accessory/{accessory_id}/buy/{id}', [AccessoryBuyController::class, 'deleteAccessoryBuy']);
    });
});
