<?php

use App\Http\Controllers\AccountController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('account', [AccountController::class, 'getAccounts']);
    Route::get('account/{code}', [AccountController::class, 'getAccountDetail']);
    Route::put('account/{code}', [AccountController::class, 'editAccount']);
    Route::post('account', [AccountController::class, 'addAccount']);
    Route::delete('account/{code}', [AccountController::class, 'deleteAccount']);
});
