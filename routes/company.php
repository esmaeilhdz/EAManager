<?php

use App\Http\Controllers\CompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('company', [CompanyController::class, 'getCompanies']);
    Route::get('company/{code}', [CompanyController::class, 'getCompanyDetail']);
    Route::put('company/{code}', [CompanyController::class, 'editCompany']);
    Route::post('company', [CompanyController::class, 'addCompany']);
    Route::delete('company/{code}', [CompanyController::class, 'deleteCompany']);
});
