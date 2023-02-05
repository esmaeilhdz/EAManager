<?php

use App\Http\Controllers\InvoiceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('invoice', [InvoiceController::class, 'getInvoices']);
    Route::get('invoice/{code}', [InvoiceController::class, 'getInvoiceDetail']);
    Route::put('invoice/{code}', [InvoiceController::class, 'editInvoice']);
    Route::post('invoice', [InvoiceController::class, 'addInvoice']);
    Route::delete('invoice/{code}', [InvoiceController::class, 'deleteInvoice']);
});
