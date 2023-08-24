<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-customer|edit-customer|view-customer']], function () {
        Route::get('customer', [CustomerController::class, 'getCustomers']);
        Route::get('customer/{code}', [CustomerController::class, 'getCustomerDetail']);
        Route::get('customer_combo', [CustomerController::class, 'getCustomerCombo']);
    });
    Route::group(['middleware' => ['permission:admin-customer|edit-customer']], function () {
        Route::put('customer/{code}', [CustomerController::class, 'editCustomer']);
    });
    Route::group(['middleware' => ['permission:admin-customer']], function () {
        Route::post('customer', [CustomerController::class, 'addCustomer']);
        Route::delete('customer/{code}', [CustomerController::class, 'deleteCustomer']);
    });
});
