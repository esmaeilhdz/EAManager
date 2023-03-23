<?php

use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-salary|edit-salary|view-salary']], function () {
        Route::get('salary', [SalaryController::class, 'getAllSalaries']);
        Route::get('person/{code}/salary', [SalaryController::class, 'getSalaries']);
        Route::get('person/{code}/salary/{id}', [SalaryController::class, 'getSalaryDetail']);
    });
    Route::group(['middleware' => ['permission:admin-salary|edit-salary']], function () {
        Route::put('person/{code}/salary/{id}', [SalaryController::class, 'editSalary']);
    });
    Route::group(['middleware' => ['permission:admin-salary']], function () {
        Route::post('person/{code}/salary', [SalaryController::class, 'addSalary']);
    });
});
