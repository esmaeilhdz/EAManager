<?php

use App\Http\Controllers\ChatGroupPersonController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('chat_group/{chat_group_id}/person', [ChatGroupPersonController::class, 'getChatGroupPersons']);
    Route::get('chat_group/{chat_group_id}/person/{id}', [ChatGroupPersonController::class, 'getChatGroupPersonDetail']);
    Route::post('chat_group/{chat_group_id}/person', [ChatGroupPersonController::class, 'addChatGroupPerson']);
    Route::delete('chat_group/{chat_group_id}/person/{id}', [ChatGroupPersonController::class, 'deleteChatGroupPerson']);
});
