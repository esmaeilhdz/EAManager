<?php

use App\Http\Controllers\CuttingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-cutting|edit-cutting|view-cutting']], function () {
        Route::get('product/{code}/cut', [CuttingController::class, 'getCuttings']);
        Route::get('product/{code}/cut/{id}', [CuttingController::class, 'getCuttingDetail']);
    });
    Route::group(['middleware' => ['permission:admin-cutting|edit-cutting']], function () {
        Route::put('product/{code}/cut/{id}', [CuttingController::class, 'editCutting']);
    });
    Route::group(['middleware' => ['permission:admin-cutting']], function () {
        Route::post('product/{code}/cut', [CuttingController::class, 'addCutting']);
        Route::delete('product/{code}/cut/{id}', [CuttingController::class, 'deleteCutting']);
    });
});
