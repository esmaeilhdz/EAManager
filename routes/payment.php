<?php

use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('payment', [PaymentController::class, 'getPayments']);
    Route::get('{resource}/{resource_id}/payment', [PaymentController::class, 'getPaymentsResource']);
    Route::get('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'getPaymentDetail']);
    Route::put('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'editPayment']);
    Route::post('{resource}/{resource_id}/payment', [PaymentController::class, 'addPayment']);
    Route::delete('{resource}/{resource_id}/payment/{id}', [PaymentController::class, 'deletePayment']);
});
