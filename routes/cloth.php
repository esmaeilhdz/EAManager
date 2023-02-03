<?php

use App\Http\Controllers\ClothController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('cloth', [ClothController::class, 'getClothes']);
    Route::get('cloth/{code}', [ClothController::class, 'getClothDetail']);
    Route::put('cloth/{code}', [ClothController::class, 'editCloth']);
    Route::post('cloth', [ClothController::class, 'addCloth']);
    Route::delete('cloth/{code}', [ClothController::class, 'deleteCloth']);
});
