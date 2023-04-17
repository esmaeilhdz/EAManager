<?php

use App\Http\Controllers\EnumerationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-enumeration|edit-enumeration|view-enumeration']], function () {
        Route::get('enumeration', [EnumerationController::class, 'getEnumerations']);
        Route::get('enumeration/{category_name}/grouped', [EnumerationController::class, 'getEnumerationGrouped']);
        Route::get('enumeration/{id}', [EnumerationController::class, 'getEnumerationDetail']);
    });
    Route::group(['middleware' => ['permission:admin-enumeration|edit-enumeration']], function () {
        Route::put('enumeration/{category_name}', [EnumerationController::class, 'editEnumeration']);
    });
    Route::group(['middleware' => ['permission:admin-enumeration']], function () {
        Route::post('enumeration', [EnumerationController::class, 'addEnumeration']);
        Route::delete('enumeration/{id}', [EnumerationController::class, 'deleteEnumeration']);
    });
});
