<?php

use App\Http\Controllers\SewingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('product/{code}/sewing', [SewingController::class, 'getSewings']);
    Route::get('product/{code}/sewing/{id}', [SewingController::class, 'getSewingDetail']);
    Route::put('product/{code}/sewing/{id}', [SewingController::class, 'editSewing']);
    Route::post('product/{code}/sewing', [SewingController::class, 'addSewing']);
    Route::delete('product/{code}/sewing/{id}', [SewingController::class, 'deleteSewing']);
});
