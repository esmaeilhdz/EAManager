<?php

use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('bill', [BillController::class, 'getBills']);
    Route::get('bill/{id}', [BillController::class, 'getBillDetail']);
    Route::put('bill/{id}', [BillController::class, 'editBill']);
    Route::post('bill', [BillController::class, 'addBill']);
    Route::delete('bill/{id}', [BillController::class, 'deleteBill']);
});
