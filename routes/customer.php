<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('customer', [CustomerController::class, 'getCustomers']);
    Route::get('customer/{code}', [CustomerController::class, 'getCustomerDetail']);
    Route::put('customer/{code}', [CustomerController::class, 'editCustomer']);
    Route::post('customer', [CustomerController::class, 'addCustomer']);
    Route::delete('customer/{code}', [CustomerController::class, 'deleteCustomer']);
});
