<?php

use App\Http\Controllers\SalePeriodController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('sale_period', [SalePeriodController::class, 'getSalePeriods']);
    Route::get('sale_period/{id}', [SalePeriodController::class, 'getSalePeriodDetail']);
    Route::put('sale_period/{id}', [SalePeriodController::class, 'editSalePeriod']);
    Route::post('sale_period', [SalePeriodController::class, 'addSalePeriod']);
    Route::delete('sale_period/{id}', [SalePeriodController::class, 'deleteSalePeriod']);
});
