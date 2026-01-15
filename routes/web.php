<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reporting\ReportingImportController;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
















use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CredentialsEmployeeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home/Dashboard
Route::get('/', function () {
    return redirect()->route('employees.index');
})->name('home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Employees Management Routes
Route::prefix('employees')->name('employees.')->group(function () {
    // List all employees
    Route::get('/', [EmployeeController::class, 'index'])->name('index');
    
    // Create employee
    Route::get('/create', [EmployeeController::class, 'create'])->name('create');
    Route::post('/', [EmployeeController::class, 'store'])->name('store');
    
    // View single employee
    Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
    
    // Edit employee
    Route::get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
    Route::put('/{employee}', [EmployeeController::class, 'update'])->name('update');
    
    // Delete employee (soft delete)
    Route::delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
    
    // Employee status management
    Route::put('/{employee}/activate', [EmployeeController::class, 'activate'])->name('activate');
    Route::put('/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('deactivate');
    
    // Restore soft deleted employee
    Route::put('/{employee}/restore', [EmployeeController::class, 'restore'])->name('restore');
    
    // Force delete employee
    Route::delete('/{employee}/force-delete', [EmployeeController::class, 'forceDelete'])->name('force-delete');
    
    // Trashed employees
    Route::get('/trashed', [EmployeeController::class, 'trashed'])->name('trashed');
});

// Roles Management Routes
Route::prefix('roles')->name('roles.')->group(function () {
    // List all roles
    Route::get('/', [RoleController::class, 'index'])->name('index');
    
    // Create role
    Route::get('/create', [RoleController::class, 'create'])->name('create');
    Route::post('/', [RoleController::class, 'store'])->name('store');
    
    // View single role
    Route::get('/{role}', [RoleController::class, 'show'])->name('show');
    
    // Edit role
    Route::get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
    Route::put('/{role}', [RoleController::class, 'update'])->name('update');
    
    // Delete role
    Route::delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
    
    // Role permissions
    Route::get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
    Route::post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
});

// Module Access Routes
Route::prefix('module-access')->name('module-access.')->group(function () {
    Route::get('/', [ModuleAccessController::class, 'index'])->name('index');
    Route::post('/', [ModuleAccessController::class, 'store'])->name('store');
    Route::get('/{moduleAccess}/edit', [ModuleAccessController::class, 'edit'])->name('edit');
    Route::put('/{moduleAccess}', [ModuleAccessController::class, 'update'])->name('update');
    Route::delete('/{moduleAccess}', [ModuleAccessController::class, 'destroy'])->name('destroy');
});

// Credentials Routes
Route::prefix('credentials')->name('credentials.')->group(function () {
    Route::get('/{employee}/edit', [CredentialsEmployeeController::class, 'edit'])->name('edit');
    Route::put('/{employee}', [CredentialsEmployeeController::class, 'update'])->name('update');
    Route::put('/{employee}/reset-password', [CredentialsEmployeeController::class, 'resetPassword'])->name('reset-password');
    Route::put('/{employee}/toggle-status', [CredentialsEmployeeController::class, 'toggleStatus'])->name('toggle-status');
});

// Profile Routes (for logged-in employee)
Route::prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'show'])->name('show');
    Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    Route::put('/', [ProfileController::class, 'update'])->name('update');
    Route::get('/change-password', [ProfileController::class, 'changePassword'])->name('change-password');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('update-password');
});

// Additional utility routes
Route::get('/search/employees', [EmployeeController::class, 'search'])->name('employees.search');
Route::get('/export/employees', [EmployeeController::class, 'export'])->name('employees.export');
Route::post('/import/employees', [EmployeeController::class, 'import'])->name('employees.import');

// API-like routes for AJAX requests
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/roles', [RoleController::class, 'apiIndex'])->name('roles.index');
    Route::get('/roles/{role}/permissions', [RoleController::class, 'apiPermissions'])->name('roles.permissions');
    Route::get('/employees/filter', [EmployeeController::class, 'filter'])->name('employees.filter');
});





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