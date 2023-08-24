<?php

use App\Http\Controllers\SalePeriodController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-sale_periods|edit-sale_periods|view-sale_periods']], function () {
        Route::get('sale_period', [SalePeriodController::class, 'getSalePeriods']);
        Route::get('sale_period/{id}', [SalePeriodController::class, 'getSalePeriodDetail']);
        Route::get('sale_period_combo', [SalePeriodController::class, 'getSalePeriodCombo']);
    });
    Route::group(['middleware' => ['permission:admin-sale_periods|edit-sale_periods']], function () {
        Route::put('sale_period/{id}', [SalePeriodController::class, 'editSalePeriod']);
    });
    Route::group(['middleware' => ['permission:admin-sale_periods']], function () {
        Route::post('sale_period', [SalePeriodController::class, 'addSalePeriod']);
        Route::delete('sale_period/{id}', [SalePeriodController::class, 'deleteSalePeriod']);
    });
});
