<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reporting\ReportingImportController;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// ============================================================================
// PUBLIC ROUTES - Accessible without authentication
// ============================================================================

// ----------------------------------------------------------------------------
// ROOT ROUTE
// ----------------------------------------------------------------------------
Route::get('/', function () {
    return view('welcome');
});

// ----------------------------------------------------------------------------
// ACTION PLANS MODULE (PUBLIC)
// ----------------------------------------------------------------------------
Route::prefix('action-plans')->name('action-plans.')->group(function () {
    Route::get('/', [ActionPlanController::class, 'index'])->name('index');
    Route::delete('/bulk-destroy', [ActionPlanController::class, 'bulkDestroy'])->name('bulk.destroy');
    Route::delete('/{id}', [ActionPlanController::class, 'destroy'])->name('destroy');
    Route::get('/{actionPlan}/download', [ActionPlanController::class, 'download'])->name('download');
});



// ----------------------------------------------------------------------------
// USERS MODULE (PUBLIC)
// ----------------------------------------------------------------------------
Route::prefix('users')->name('users.')->group(function () {
    // CRUD Operations
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{user_id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user_id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user_id}', [UserController::class, 'destroy'])->name('destroy');
    
    // Bulk Operations
    Route::post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk.destroy');
    
    // Import/Export
    Route::get('/import', [UserController::class, 'importForm'])->name('import.form');
    Route::post('/import', [UserController::class, 'import'])->name('import');
    Route::get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
    Route::get('/export', [UserController::class, 'exportExcel'])->name('export.excel');
    
    // Statistics
    Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
});

// ----------------------------------------------------------------------------
// ACTIVITIES MODULE (PUBLIC)
// ----------------------------------------------------------------------------
Route::prefix('activities')->name('activities.')->group(function () {
    // Main CRUD Routes
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::get('/create', [ActivityController::class, 'create'])->name('create');
    Route::post('/', [ActivityController::class, 'store'])->name('store');
    Route::get('/{activity}', [ActivityController::class, 'edit'])->name('edit');
    Route::put('/{activity}', [ActivityController::class, 'update'])->name('update');
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
    
    // Bulk Operations
    Route::delete('/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('bulk.destroy');
    
    // Child Activities
    Route::prefix('{parentActivity}/children')->name('children.')->group(function () {
        Route::get('/', [ActivityController::class, 'indexChildren'])->name('index');
        Route::get('/create', [ActivityController::class, 'createChild'])->name('create');
        Route::post('/', [ActivityController::class, 'storeChild'])->name('store');
    });
    
    // AJAX Helper Routes
    Route::prefix('ajax')->group(function () {
        Route::get('/get-rp-activities', [ActivityController::class, 'getRPActivities'])->name('get-rp-activities');
        Route::get('/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])->name('get-rp-actions-with-activities');
        Route::get('/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])->name('get-projects-by-program');
        Route::get('/get-action-plans', [ActivityController::class, 'getActionPlans'])->name('get-action-plans');
        Route::get('/get-components-by-action-plan', [ActivityController::class, 'getComponentsByActionPlan'])->name('get-components-by-action-plan');
        Route::get('/get-rp-components', [ActivityController::class, 'getRPComponents'])->name('get-rp-components');
    });
});

// ----------------------------------------------------------------------------
// REPORTING MODULE (PUBLIC)
// ----------------------------------------------------------------------------
Route::prefix('reporting')->name('reporting.')->group(function () {
    Route::get('/import', [ReportingImportController::class, 'index'])->name('import.import');
    Route::post('/import', [ReportingImportController::class, 'import'])->name('import.process');
    Route::post('/import/preview', [ReportingImportController::class, 'preview'])->name('import.preview');
    Route::get('/import/template', [ReportingImportController::class, 'downloadTemplate'])->name('import.download-template');
    Route::post('/reporting/import/process', [ReportingImportController::class, 'process'])->name('reporting.import.process');
});

// ============================================================================
// AUTHENTICATION ROUTES
// ============================================================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================================
// PROTECTED ROUTES - Authentication Required
// ============================================================================
Route::middleware(['auth:employee'])->group(function () {
    
    // ------------------------------------------------------------------------
    // DASHBOARD - Accessible to all authenticated employees
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ========================================================================
    // PROTECTED MODULES - Require module access
    // ========================================================================
    
    // ------------------------------------------------------------------------
    // PROTECTED ACTIVITIES MODULE
    // ------------------------------------------------------------------------
    Route::middleware([\App\Http\Middleware\CheckModuleAccess::class . ':activities,view'])
         ->prefix('protected/activities')
         ->name('protected.activities.')
         ->group(function () {
             
             // Listing
             Route::get('/', [ActivityController::class, 'index'])->name('index');
             
             // Create (requires create access)
             Route::middleware(['module.access:activities,create'])->group(function () {
                 Route::get('/create', [ActivityController::class, 'create'])->name('create');
                 Route::post('/', [ActivityController::class, 'store'])->name('store');
             });
             
             // Single Activity Operations
             Route::prefix('{activity}')->group(function () {
                 // View (requires view access)
                 Route::get('/', [ActivityController::class, 'show'])
                      ->middleware(['resource.access:view'])
                      ->name('show');
                 
                 // Edit/Update (requires edit access)
                 Route::middleware(['resource.access:edit'])->group(function () {
                     Route::get('/edit', [ActivityController::class, 'edit'])->name('edit');
                     Route::put('/', [ActivityController::class, 'update'])->name('update');
                 });
                 
                 // Destroy (requires delete access)
                 Route::delete('/', [ActivityController::class, 'destroy'])
                      ->middleware(['resource.access:delete'])
                      ->name('destroy');
             });
         });
    
    // ------------------------------------------------------------------------
    // PROTECTED USERS MODULE
    // ------------------------------------------------------------------------
    Route::middleware(\App\Http\Middleware\CheckModuleAccess::class . ':users,view')
         ->prefix('protected/users')
         ->name('protected.users.')
         ->group(function () {
             
             // Listing
             Route::get('/', [UserController::class, 'index'])->name('index');
             
             // Create (requires create access)
             Route::middleware(['module.access:users,create'])->group(function () {
                 Route::get('/create', [UserController::class, 'create'])->name('create');
                 Route::post('/', [UserController::class, 'store'])->name('store');
             });
             
             // Single User Operations
             Route::prefix('{user}')->group(function () {
                 // View (requires view access)
                 Route::get('/', [UserController::class, 'show'])
                      ->middleware(['resource.access:view'])
                      ->name('show');
                 
                 // Edit/Update (requires edit access)
                 Route::middleware(['resource.access:edit'])->group(function () {
                     Route::get('/edit', [UserController::class, 'edit'])->name('edit');
                     Route::put('/', [UserController::class, 'update'])->name('update');
                 });
             });
         });
    
   
}); 