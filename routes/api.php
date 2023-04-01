<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PersonController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'v1'], function () {
    Route::get('date', [GeneralController::class, 'getDate']);
    Route::get('captcha', [AuthController::class, 'getCaptcha']);
    Route::post('login', [AuthController::class, 'login']);
});

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('menu', [MenuController::class, 'getMenu']);
    Route::delete('logout', [AuthController::class, 'logOut']);
});
