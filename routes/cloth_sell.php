<?php

use App\Http\Controllers\ClothSellController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth|view-cloth']], function () {
        Route::get('cloth/{code}/sell', [ClothSellController::class, 'getClothSells']);
        Route::get('cloth/{code}/sell/{id}', [ClothSellController::class, 'getClothSellDetail']);
    });
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth']], function () {
        Route::put('cloth/{code}/sell/{id}', [ClothSellController::class, 'editClothSell']);
    });
    Route::group(['middleware' => ['permission:admin-cloth']], function () {
        Route::post('cloth/{code}/sell', [ClothSellController::class, 'addClothSell']);
        Route::delete('cloth/{code}/sell/{id}', [ClothSellController::class, 'deleteClothSell']);
    });
});
