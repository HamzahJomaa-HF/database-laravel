<?php 

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityController;

Route::get('/users', [UserController::class, 'index']);

Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

// Route::apiResource('/activities', ActivityController::class);