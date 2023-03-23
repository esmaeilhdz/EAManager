<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-payment|edit-payment|view-payment']], function () {
        Route::get('payment', [PaymentController::class, 'getPayments']);
        Route::get('{resource}/{resource_id}/payment', [PaymentController::class, 'getPaymentsResource']);
        Route::get('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'getPaymentDetail']);
    });
    Route::group(['middleware' => ['permission:admin-payment|edit-payment']], function () {
        Route::put('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'editPayment']);
    });
    Route::group(['middleware' => ['permission:admin-payment']], function () {
        Route::post('{resource}/{resource_id}/payment', [PaymentController::class, 'addPayment']);
        Route::delete('{resource}/{resource_id}/payment', [PaymentController::class, 'deletePaymentsResource']);
        Route::delete('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'deletePayment']);
    });
});
