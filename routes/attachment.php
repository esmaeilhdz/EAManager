<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::group(['middleware' => ['permission:admin-attachment|edit-attachment|view-attachment']], function () {
        Route::get('{resource}/{resource_id}/attachment', [AttachmentController::class, 'getAttachments']);
    });
    Route::group(['middleware' => ['permission:admin-attachment']], function () {
        Route::post('{resource}/{resource_id}/attachment', [AttachmentController::class, 'addAttachment']);
        Route::delete('{resource}/{resource_id}/attachment/{code}', [AttachmentController::class, 'deleteAttachment']);
    });
});
