<?php 

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityController;

Route::get('/users', [UserController::class, 'index']);

Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

use App\Http\Controllers\API\ProgramController;
Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'index']);
    Route::post('/', [ProgramController::class, 'store']);
    Route::put('/{id}', [ProgramController::class, 'update']);
    Route::delete('/{id}', [ProgramController::class, 'destroy']);
});


use App\Http\Controllers\API\ProjectCenterController;
use Illuminate\Support\Facades\Route;

Route::get('/project-centers', [ProjectCenterController::class, 'index']);
Route::post('/project-centers', [ProjectCenterController::class, 'store']);
Route::put('/project-centers/{id}', [ProjectCenterController::class, 'update']);
Route::delete('/project-centers/{id}', [ProjectCenterController::class, 'destroy']);


// Route::apiResource('/activities', ActivityController::class);