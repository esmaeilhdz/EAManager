<?php

use App\Http\Controllers\SewingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-sewing|edit-sewing|view-sewing']], function () {
        Route::get('product/{code}/sewing', [SewingController::class, 'getSewings']);
        Route::get('product/{code}/sewing/{id}', [SewingController::class, 'getSewingDetail']);
    });
    Route::group(['middleware' => ['permission:admin-sewing|edit-sewing']], function () {
        Route::put('product/{code}/sewing/{id}', [SewingController::class, 'editSewing']);
    });
    Route::group(['middleware' => ['permission:admin-sewing']], function () {
        Route::post('product/{code}/sewing', [SewingController::class, 'addSewing']);
        Route::delete('product/{code}/sewing/{id}', [SewingController::class, 'deleteSewing']);
    });
});
