<?php

use App\Http\Controllers\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('{resource}/{resource_id}/attachment', [AttachmentController::class, 'getAttachments']);
    Route::post('{resource}/{resource_id}/attachment', [AttachmentController::class, 'addAttachment']);
    Route::delete('{resource}/{resource_id}/attachment/{code}', [AttachmentController::class, 'deleteAttachment']);
});
