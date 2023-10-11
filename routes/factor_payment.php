<?php

use App\Http\Controllers\FactorPaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-factor|edit-factor|view-factor']], function () {
        Route::get('factor/{code}/payment', [FactorPaymentController::class, 'getFactorPayments']);
        Route::get('factor/{code}/payment/{id}', [FactorPaymentController::class, 'getFactorPaymentDetail']);
    });
    Route::group(['middleware' => ['permission:admin-factor']], function () {
        Route::post('factor/{code}/payment', [FactorPaymentController::class, 'addFactorPayment']);
        Route::delete('factor/{code}/payment/{id}', [FactorPaymentController::class, 'deleteFactorPayment']);
    });
});
