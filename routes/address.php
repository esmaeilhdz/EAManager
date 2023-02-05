<?php

use App\Http\Controllers\AddressController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::put('{resource}/{resource_id}/address/{id}', [AddressController::class, 'editAddress']);
    Route::post('{resource}/{resource_id}/address', [AddressController::class, 'addAddress']);
    Route::delete('{resource}/{resource_id}/address/{id}', [AddressController::class, 'deleteAddress']);
});
