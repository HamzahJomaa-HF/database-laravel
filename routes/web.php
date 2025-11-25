<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

Route::get('/', function () {
    return view('welcome');
});

// Use {user_id} to match your controller methods
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit'); // Changed to user_id
Route::put('/users/{user_id}', [UserController::class, 'update'])->name('users.update'); // Changed to user_id
Route::delete('/users/{user_id}', [UserController::class, 'destroy'])->name('users.destroy'); // Changed to user_id
Route::get('/users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');

// Auth routes
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');