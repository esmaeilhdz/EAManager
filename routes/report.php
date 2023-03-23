<?php

use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1/report', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-report|edit-report|view-report']], function () {
        Route::get('debtor_customer', [ReportController::class, 'getDebtorCustomers']);
    });
});
