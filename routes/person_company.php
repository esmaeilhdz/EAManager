<?php

use App\Http\Controllers\PersonCompanyController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-person|edit-person|view-person|admin-company|edit-company|view-company']], function () {
        Route::get('person/{person_code}/company', [PersonCompanyController::class, 'getCompaniesOfPerson']);
        Route::get('person/{person_code}/company/{company_code}', [PersonCompanyController::class, 'getCompanyOfPerson']);
    });
    Route::group(['middleware' => ['permission:admin-person|edit-person|admin-company|edit-company']], function () {
        Route::put('person/{person_code}/company/{company_code}', [PersonCompanyController::class, 'editPersonCompany']);
    });
    Route::group(['middleware' => ['permission:admin-person|admin-company']], function () {
        Route::post('person/{person_code}/company', [PersonCompanyController::class, 'addPersonCompany']);
        Route::delete('person/{person_code}/company/{company_code}', [PersonCompanyController::class, 'deletePersonCompany']);
    });
});
