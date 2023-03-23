<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-chat|edit-chat|view-chat']], function () {
        Route::get('{resource}/{resource_id}/chat', [ChatController::class, 'getChats']);
        Route::get('{resource}/{resource_id}/chat/{id}', [ChatController::class, 'getChatDetail']);
    });
});
