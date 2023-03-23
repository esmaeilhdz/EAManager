<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-address|edit-address']], function () {
        Route::put('{resource}/{resource_id}/address/{id}', [AddressController::class, 'editAddress']);
    });
    Route::group(['middleware' => ['permission:admin-address']], function () {
        Route::post('{resource}/{resource_id}/address', [AddressController::class, 'addAddress']);
        Route::delete('{resource}/{resource_id}/address/{id}', [AddressController::class, 'deleteAddress']);
    });
});
