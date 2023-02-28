<?php

use App\Http\Controllers\ChatGroupController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('chat_group', [ChatGroupController::class, 'getChatGroups']);
    Route::get('chat_group/{id}', [ChatGroupController::class, 'getChatGroupDetail']);
    Route::put('chat_group/{id}', [ChatGroupController::class, 'editChatGroup']);
    Route::post('chat_group', [ChatGroupController::class, 'addChatGroup']);
    Route::delete('chat_group/{id}', [ChatGroupController::class, 'deleteChatGroup']);
});
