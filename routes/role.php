<?php

use App\Http\Controllers\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-role|edit-role|view-role']], function () {
        Route::get('role', [RoleController::class, 'getRoles']);
        Route::get('role_tree', [RoleController::class, 'getRolesTree']);
        Route::get('role/{code}', [RoleController::class, 'getRoleDetail']);
    });
    Route::group(['middleware' => ['permission:admin-role|edit-role']], function () {
        Route::put('role/{code}', [RoleController::class, 'editRole']);
    });
    Route::group(['middleware' => ['permission:admin-role']], function () {
        Route::post('role', [RoleController::class, 'addRole']);
    });
});
