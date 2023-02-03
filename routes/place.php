<?php

use App\Http\Controllers\PlaceController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('place', [PlaceController::class, 'getPlaces']);
    Route::get('place/{id}', [PlaceController::class, 'getPlaceDetail']);
    Route::put('place/{id}', [PlaceController::class, 'editPlace']);
    Route::post('place', [PlaceController::class, 'addPlace']);
    Route::delete('place/{id}', [PlaceController::class, 'deletePlace']);
});
