<?php

use App\Http\Controllers\GroupConversationController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('group_conversation', [GroupConversationController::class, 'getGroupConversations']);
    Route::get('group_conversation/{id}', [GroupConversationController::class, 'getGroupConversationDetail']);
    Route::put('group_conversation/{id}', [GroupConversationController::class, 'editGroupConversation']);
    Route::post('group_conversation', [GroupConversationController::class, 'addGroupConversation']);
    Route::delete('group_conversation/{id}', [GroupConversationController::class, 'deleteGroupConversation']);
});
