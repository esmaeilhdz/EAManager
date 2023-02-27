<?php

use App\Http\Controllers\DesignModelController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {
    Route::get('design_model', [DesignModelController::class, 'getDesignModels']);
    Route::get('design_model/{id}', [DesignModelController::class, 'getDesignModelDetail']);
    Route::put('design_model/{id}', [DesignModelController::class, 'editDesignModel']);
    Route::patch('design_model/{id}', [DesignModelController::class, 'confirmDesignModel']);
    Route::post('design_model', [DesignModelController::class, 'addDesignModel']);
    Route::delete('design_model/{id}', [DesignModelController::class, 'deleteDesignModel']);
});
