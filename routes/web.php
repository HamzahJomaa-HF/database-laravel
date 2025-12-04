<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\ActivityController;

Route::get('/', function () {
    return view('welcome');
});



Route::get('/activity/{external_activity_id}/link', [ActivityController::class, 'index'])->name('link.activities'); // Example route for ActivityController

// Use {user_id} to match your controller methods
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit'); // Changed to user_id
Route::put('/users/{user_id}', [UserController::class, 'update'])->name('users.update'); // Changed to user_id
Route::delete('/users/{user_id}', [UserController::class, 'destroy'])->name('users.destroy'); // Changed to user_id
Route::get('/users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');

// WEB Analytics routes (return views)
Route::get('/users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
Route::get('/users/statistics', [UserController::class, 'statistics'])->name('users.statistics');
Route::get('/users/reports', [UserController::class, 'reports'])->name('users.reports');
// Auth routes
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Export route
Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');

// import functionality
Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import.form');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');

// Bulk actions
Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
Route::delete('/users/delete-all', [UserController::class, 'deleteAll'])->name('users.delete-all');




use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes - NO middleware needed
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resend.otp');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Protected routes - Require authentication
Route::middleware(['auth.system'])->group(function () {
    Route::get('/dashboard', function () {
        // Simple dashboard view
        $user = auth()->guard('system')->user();
        return view('dashboard', ['user' => $user]);
    })->name('dashboard');
    
    // Add more protected routes here if needed
});