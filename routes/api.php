<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TokenController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\SurveyController;
use App\Http\Controllers\API\CopController;
use App\Http\Controllers\API\ActivityUserController;
use App\Http\Controllers\API\PortfolioController;
use App\Http\Controllers\API\DiplomaController;
use App\Http\Controllers\API\UserDiplomaController;
use App\Http\Controllers\API\NationalityController;
use App\Http\Controllers\API\UserNationalityController;
use App\Http\Controllers\API\ProjectPortfolioController;

// ============================================================================
// TOKEN GENERATION (Uses session auth - for employees to get tokens)
// ============================================================================
Route::middleware(['auth:employee'])->post('/tokens/generate', [TokenController::class, 'generate']);

// ============================================================================
// PROTECTED API ROUTES (Require Sanctum token)
// ============================================================================
Route::middleware(['auth:sanctum'])->group(function () {
    
    // ========================================================================
    // EMPLOYEE ENDPOINTS (with permissions)
    // ========================================================================
    Route::get('/employees/me', [EmployeeController::class, 'me']);
    Route::middleware(['hasPermission:Employees.view,Employees.manage,Employees.full'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index']);
    });
    
    // ========================================================================
    // USER ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])->group(function () {
        Route::get('/users', [UserController::class, 'index']);
    });
    
    // ========================================================================
    // ACTIVITY ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:Activities.view,Activities.manage,Activities.full'])->group(function () {
        Route::get('/activities', [ActivityController::class, 'index']);
    });
    
    // ========================================================================
    // PROGRAM ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:programs.view,programs.manage,programs.full'])->group(function () {
        Route::get('/programs', [ProgramController::class, 'index']);
    });
    
    // ========================================================================
    // PROJECT ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:projects.view,projects.manage,projects.full'])->group(function () {
        Route::get('/projects', [ProjectController::class, 'index']);
    });
    
    // ========================================================================
    // SURVEY ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:surveys.view,surveys.manage,surveys.full'])->group(function () {
        Route::get('/surveys', [SurveyController::class, 'index']);
    });
    
    // ========================================================================
    // COP ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:COPs.view,COPs.manage,COPs.full'])->group(function () {
        Route::get('/cops', [CopController::class, 'index']);
    });
    
    // ========================================================================
    // ACTIVITY USERS ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:ActivityUsers.view,ActivityUsers.manage,ActivityUsers.full'])->group(function () {
        Route::get('/activity-users', [ActivityUserController::class, 'index']);
    });
    
    // ========================================================================
    // PORTFOLIO ENDPOINTS (with permissions)
    // ========================================================================
    Route::middleware(['hasPermission:Portfolios.view,Portfolios.manage,Portfolios.full'])->group(function () {
        Route::get('/portfolios', [PortfolioController::class, 'index']);
        Route::apiResource('project-portfolios', ProjectPortfolioController::class);
        Route::get('portfolios/{portfolioId}/projects', [ProjectPortfolioController::class, 'getProjectsByPortfolio']);
        Route::get('projects/{projectId}/portfolios', [ProjectPortfolioController::class, 'getPortfoliosByProject']);
    });
    
    // ========================================================================
    // DIPLOMA ENDPOINTS (Authentication required, no permission check)
    // ========================================================================
    Route::get('/diplomas', [DiplomaController::class, 'index']);
    Route::get('/user-diplomas', [UserDiplomaController::class, 'index']);
    
    // ========================================================================
    // NATIONALITY ENDPOINTS (Authentication required, no permission check)
    // ========================================================================
    Route::get('/nationalities', [NationalityController::class, 'index']);
    Route::get('/user-nationalities', [UserNationalityController::class, 'index']);
    
});