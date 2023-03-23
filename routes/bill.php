<?php

use App\Http\Controllers\BillController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-bill|edit-bill|view-bill']], function () {
        Route::get('bill', [BillController::class, 'getBills']);
        Route::get('bill/{id}', [BillController::class, 'getBillDetail']);
    });
    Route::group(['middleware' => ['permission:admin-bill|edit-bill']], function () {
        Route::put('bill/{id}', [BillController::class, 'editBill']);
    });
    Route::group(['middleware' => ['permission:admin-bill']], function () {
        Route::post('bill', [BillController::class, 'addBill']);
        Route::delete('bill/{id}', [BillController::class, 'deleteBill']);
    });
});
