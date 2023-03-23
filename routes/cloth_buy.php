<?php

use App\Http\Controllers\ClothBuyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth|view-cloth']], function () {
        Route::get('cloth/{code}/buy', [ClothBuyController::class, 'getClothBuys']);
        Route::get('cloth/{code}/buy/{id}', [ClothBuyController::class, 'getClothBuyDetail']);
    });
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth']], function () {
        Route::put('cloth/{code}/buy/{id}', [ClothBuyController::class, 'editClothBuy']);
    });
    Route::group(['middleware' => ['permission:admin-cloth']], function () {
        Route::post('cloth/{code}/buy', [ClothBuyController::class, 'addClothBuy']);
        Route::delete('cloth/{code}/buy/{id}', [ClothBuyController::class, 'deleteClothBuy']);
    });
});
