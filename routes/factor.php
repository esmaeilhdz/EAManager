<?php

use App\Http\Controllers\FactorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-factor|edit-factor|view-factor']], function () {
        Route::get('factor', [FactorController::class, 'getFactors']);
        Route::get('factor_completable', [FactorController::class, 'getCompletableFactors']);
        Route::get('factor/{code}', [FactorController::class, 'getFactorDetail']);
    });
    Route::group(['middleware' => ['permission:admin-factor|edit-factor']], function () {
        Route::put('factor/{code}', [FactorController::class, 'editFactor']);
        Route::patch('factor/{code}', [FactorController::class, 'changeStatusFactor']);
    });
    Route::group(['middleware' => ['permission:admin-factor']], function () {
        Route::post('factor', [FactorController::class, 'addFactor']);
        Route::delete('factor/{code}', [FactorController::class, 'deleteFactor']);
    });
});
