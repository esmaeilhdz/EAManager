<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('user_info', [UserController::class, 'getUserInfo']);
    Route::group(['middleware' => ['permission:admin-user|edit-user|view-user']], function () {
        Route::get('user', [UserController::class, 'getUsers']);
        Route::get('user/{code}', [UserController::class, 'getUserDetail']);
    });
    Route::group(['middleware' => ['permission:admin-user|edit-user']], function () {
        Route::put('user/{code}', [UserController::class, 'editUser']);
    });
    Route::group(['middleware' => ['permission:admin-user']], function () {
        Route::post('user', [UserController::class, 'addUser']);
        Route::delete('user/{code}', [UserController::class, 'deleteUser']);
    });
});
