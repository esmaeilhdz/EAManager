<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-invoice|edit-invoice|view-invoice']], function () {
        Route::get('invoice', [InvoiceController::class, 'getInvoices']);
        Route::get('invoice/{code}', [InvoiceController::class, 'getInvoiceDetail']);
    });
    Route::group(['middleware' => ['permission:admin-invoice|edit-invoice']], function () {
        Route::put('invoice/{code}', [InvoiceController::class, 'editInvoice']);
    });
    Route::group(['middleware' => ['permission:admin-invoice']], function () {
        Route::post('invoice', [InvoiceController::class, 'addInvoice']);
        Route::delete('invoice/{code}', [InvoiceController::class, 'deleteInvoice']);
    });
});
