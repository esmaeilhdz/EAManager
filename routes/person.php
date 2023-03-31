<?php

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-person|edit-person|view-person']], function () {
        Route::get('person', [PersonController::class, 'getPersons']);
        Route::get('person_combo', [PersonController::class, 'getPersonsCombo']);
        Route::get('person/{code}', [PersonController::class, 'getPersonDetail']);
    });
    Route::group(['middleware' => ['permission:admin-person|edit-person']], function () {
        Route::put('person/{code}', [PersonController::class, 'editPerson']);
    });
    Route::group(['middleware' => ['permission:admin-person']], function () {
        Route::post('person', [PersonController::class, 'addPerson']);
        Route::delete('person/{code}', [PersonController::class, 'deletePerson']);
    });
});
