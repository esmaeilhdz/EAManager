<?php

use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-place|edit-place|view-place']], function () {
        Route::get('place', [PlaceController::class, 'getPlaces']);
        Route::get('place/{id}', [PlaceController::class, 'getPlaceDetail']);
    });
    Route::group(['middleware' => ['permission:admin-place|edit-place']], function () {
        Route::put('place/{id}', [PlaceController::class, 'editPlace']);
    });
    Route::group(['middleware' => ['permission:admin-place']], function () {
        Route::post('place', [PlaceController::class, 'addPlace']);
        Route::delete('place/{id}', [PlaceController::class, 'deletePlace']);
    });
});
