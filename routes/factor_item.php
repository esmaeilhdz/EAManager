<?php

use App\Http\Controllers\FactorItemController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-factor|edit-factor|view-factor']], function () {
        Route::get('factor/{code}/item', [FactorItemController::class, 'getFactorItems']);
        Route::get('factor/{code}/item/{id}', [FactorItemController::class, 'getFactorItemDetail']);
    });
    Route::group(['middleware' => ['permission:admin-factor']], function () {
        Route::post('factor/{code}/item', [FactorItemController::class, 'addFactorItem']);
        Route::delete('factor/{code}/item/{id}', [FactorItemController::class, 'deleteFactorItem']);
    });
});
