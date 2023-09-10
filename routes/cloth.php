<?php

use App\Http\Controllers\ClothController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth|view-cloth']], function () {
        Route::get('cloth', [ClothController::class, 'getClothes']);
        Route::get('cloth/{code}', [ClothController::class, 'getClothDetail']);
        Route::get('cloth_combo', [ClothController::class, 'getClothCombo']);
    });
    Route::group(['middleware' => ['permission:admin-cloth|edit-cloth']], function () {
        Route::put('cloth/{code}', [ClothController::class, 'editCloth']);
    });
    Route::group(['middleware' => ['permission:admin-cloth']], function () {
        Route::post('cloth', [ClothController::class, 'addCloth']);
        Route::delete('cloth/{code}', [ClothController::class, 'deleteCloth']);
    });
});
