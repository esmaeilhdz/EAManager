<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-account|edit-account|view-account']], function () {
        Route::get('account', [AccountController::class, 'getAccounts']);
        Route::get('account/{code}', [AccountController::class, 'getAccountDetail']);
    });
    Route::group(['middleware' => ['permission:admin-account|edit-account']], function () {
        Route::put('account/{code}', [AccountController::class, 'editAccount']);
    });
    Route::group(['middleware' => ['permission:admin-account']], function () {
        Route::post('account', [AccountController::class, 'addAccount']);
        Route::delete('account/{code}', [AccountController::class, 'deleteAccount']);
    });
});
