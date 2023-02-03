<?php

use App\Http\Controllers\NotifController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('notif', [NotifController::class, 'getNotifs']);
    Route::get('notif/{code}', [NotifController::class, 'getNotifDetail']);
    Route::put('notif/{code}', [NotifController::class, 'editNotif']);
    Route::post('notif', [NotifController::class, 'addNotif']);
    Route::delete('notif/{code}', [NotifController::class, 'deleteNotif']);
});
