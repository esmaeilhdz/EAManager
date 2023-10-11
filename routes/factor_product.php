<?php

use App\Http\Controllers\FactorProductController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-factor|edit-factor|view-factor']], function () {
        Route::get('factor/{code}/product', [FactorProductController::class, 'getFactorProducts']);
        Route::get('factor/{code}/product/{id}', [FactorProductController::class, 'getFactorProductDetail']);
    });
    Route::group(['middleware' => ['permission:admin-factor']], function () {
        Route::post('factor/{code}/product', [FactorProductController::class, 'addFactorProduct']);
        Route::delete('factor/{code}/product/{id}', [FactorProductController::class, 'deleteFactorProduct']);
    });
});
