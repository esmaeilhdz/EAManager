<?php

use App\Http\Controllers\SalaryController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('salary', [SalaryController::class, 'getAllSalaries']);
    Route::get('person/{code}/salary', [SalaryController::class, 'getSalaries']);
    Route::get('person/{code}/salary/{id}', [SalaryController::class, 'getSalaryDetail']);
    Route::put('person/{code}/salary/{id}', [SalaryController::class, 'editSalary']);
    Route::post('person/{code}/salary', [SalaryController::class, 'addSalary']);
});
