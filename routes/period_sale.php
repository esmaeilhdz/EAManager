<?php

use App\Http\Controllers\PeriodSaleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('period_sale', [PeriodSaleController::class, 'getPeriodSales']);
    Route::get('period_sale/{id}', [PeriodSaleController::class, 'getPeriodSaleDetail']);
    Route::put('period_sale/{id}', [PeriodSaleController::class, 'editPeriodSale']);
    Route::post('period_sale', [PeriodSaleController::class, 'addPeriodSale']);
    Route::delete('period_sale/{id}', [PeriodSaleController::class, 'deletePeriodSale']);
});
