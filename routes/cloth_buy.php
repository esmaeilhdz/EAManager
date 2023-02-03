<?php

use App\Http\Controllers\ClothBuyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('cloth/{code}/buy', [ClothBuyController::class, 'getClothBuys']);
    Route::get('cloth/{code}/buy/{id}', [ClothBuyController::class, 'getClothBuyDetail']);
    Route::put('cloth/{code}/buy/{id}', [ClothBuyController::class, 'editClothBuy']);
    Route::post('cloth/{code}/buy', [ClothBuyController::class, 'addClothBuy']);
    Route::delete('cloth/{code}/buy/{id}', [ClothBuyController::class, 'deleteClothBuy']);
});
