<?php

use App\Http\Controllers\FactorController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('factor', [FactorController::class, 'getFactors']);
    Route::get('factor_completable', [FactorController::class, 'getCompletableFactors']);
    Route::get('factor/{code}', [FactorController::class, 'getFactorDetail']);
    Route::put('factor/{code}', [FactorController::class, 'editFactor']);
    Route::patch('factor/{code}/complete', [FactorController::class, 'changeCompleteFactor']);
    Route::post('factor', [FactorController::class, 'addFactor']);
    Route::delete('factor/{code}', [FactorController::class, 'deleteFactor']);
});
