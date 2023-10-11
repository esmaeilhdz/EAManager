<?php

use App\Http\Controllers\ProductModelController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-product|edit-product|view-product']], function () {
        Route::get('product/{code}/model', [ProductModelController::class, 'getProductModels']);
        Route::get('product/{code}/model/{id}', [ProductModelController::class, 'getProductsModelDetail']);
        Route::get('product/{code}/model_combo', [ProductModelController::class, 'getProductModelCombo']);
        Route::get('product_model_combo', [ProductModelController::class, 'getProductsModelCombo']);
    });
    Route::group(['middleware' => ['permission:admin-product|edit-product']], function () {
        Route::put('product/{code}/model/{id}', [ProductModelController::class, 'editProductModel']);
    });
    Route::group(['middleware' => ['permission:admin-product']], function () {
        Route::post('product/{code}/model', [ProductModelController::class, 'addProductModel']);
        Route::delete('product/{code}/model/{id}', [ProductModelController::class, 'deleteProductModel']);
    });
});
