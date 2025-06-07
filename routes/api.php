<?php

use App\Http\Controllers\Api\AlertController;
use App\Http\Controllers\Api\StatusController;
use App\Http\Controllers\Api\TargetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:api')->group(function () {
});

Route::apiResource('targets', TargetController::class);
Route::get('status/{target}', [StatusController::class, 'latest']);
Route::get('history/{target}', [StatusController::class, 'history']);
Route::get('alerts', [AlertController::class, 'index']);

