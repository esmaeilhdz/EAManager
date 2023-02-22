<?php

use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('person', [PersonController::class, 'getPersons']);
    Route::get('person/{code}', [PersonController::class, 'getPersonDetail']);
    Route::put('person/{code}', [PersonController::class, 'editPerson']);
    Route::post('person', [PersonController::class, 'addPerson']);
    Route::delete('person/{code}', [PersonController::class, 'deletePerson']);
});
