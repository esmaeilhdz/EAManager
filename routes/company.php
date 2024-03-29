<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-company|edit-company|view-company']], function () {
        Route::get('company', [CompanyController::class, 'getCompanies']);
        Route::get('company_combo', [CompanyController::class, 'getCompanyCombo']);
        Route::get('company/{code}', [CompanyController::class, 'getCompanyDetail']);
    });
    Route::group(['middleware' => ['permission:admin-company|edit-company']], function () {
        Route::put('company/{code}', [CompanyController::class, 'editCompany']);
        Route::patch('company/{code}', [CompanyController::class, 'editCompanyStatus']);
    });
    Route::group(['middleware' => ['permission:admin-company']], function () {
        Route::post('company', [CompanyController::class, 'addCompany']);
        Route::delete('company/{code}', [CompanyController::class, 'deleteCompany']);
    });
});
