<?php

use App\Http\Controllers\SalaryDeductionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-salary|edit-salary|view-salary']], function () {
        Route::get('salary/{salary_id}/deduction', [SalaryDeductionController::class, 'getSalaryDeductions']);
        Route::get('salary/{salary_id}/deduction/{id}', [SalaryDeductionController::class, 'getSalaryDeductionDetail']);
    });
    Route::group(['middleware' => ['permission:admin-salary|edit-salary']], function () {
        Route::put('salary/{salary_id}/deduction/{id}', [SalaryDeductionController::class, 'editSalaryDeduction']);
    });
    Route::group(['middleware' => ['permission:admin-salary']], function () {
        Route::post('salary/{salary_id}/deduction', [SalaryDeductionController::class, 'addSalaryDeduction']);
        Route::delete('salary/{salary_id}/deduction/{id}', [SalaryDeductionController::class, 'deleteSalaryDeduction']);
    });
});
