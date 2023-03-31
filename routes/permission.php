<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-role|edit-role|view-role']], function () {
        Route::get('role/{code}/permission', [PermissionController::class, 'getRolePermissions']);
    });
    Route::group(['middleware' => ['permission:admin-role|edit-role']], function () {
        Route::put('role/{code}/permission/{id}', [PermissionController::class, 'editRolePermissions']);
    });
});
