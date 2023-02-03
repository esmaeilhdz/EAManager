<?php

use App\Http\Controllers\ProductToStoreController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('product/{code}/to_store', [ProductToStoreController::class, 'getProductToStores']);
    Route::get('product/{code}/to_store/{id}', [ProductToStoreController::class, 'getProductToStoreDetail']);
    Route::put('product/{code}/to_store/{id}', [ProductToStoreController::class, 'editProductToStore']);
    Route::post('product/{code}/to_store', [ProductToStoreController::class, 'addProductToStore']);
    Route::delete('product/{code}/to_store/{id}', [ProductToStoreController::class, 'deleteProductToStore']);
});
