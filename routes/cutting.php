<?php

use App\Http\Controllers\CuttingController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('product/{code}/cut', [CuttingController::class, 'getCuttings']);
    Route::get('product/{code}/cut/{id}', [CuttingController::class, 'getCuttingDetail']);
    Route::put('product/{code}/cut/{id}', [CuttingController::class, 'editCutting']);
    Route::post('product/{code}/cut', [CuttingController::class, 'addCutting']);
    Route::delete('product/{code}/cut/{id}', [CuttingController::class, 'deleteCutting']);
});
