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
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ModuleAccessController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\CopController;



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

// ============================================================================
// PUBLIC ROUTES - Accessible WITHOUT authentication
// ============================================================================


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/


// CREATE ROUTES
Route::get('/center/create', [ProgramController::class, 'createCenter'])->name('createCenter');
Route::get('/program/create', [ProgramController::class, 'createFlagshipLocal'])->name('create.flagshiplocal');
Route::get('/subprogram/create', [ProgramController::class, 'createSubprogram'])->name('create.subprogram');

// STORE ROUTES (POST)
Route::post('/center/create', [ProgramController::class, 'storeCenter'])->name('storeCenter');
Route::post('/program/create', [ProgramController::class, 'storeFlagshipLocal'])->name('storeFlagshipLocal');
Route::post('/subprogram/create', [ProgramController::class, 'storeSubprogram'])->name('storeSubprogram');

// EDIT ROUTES 
Route::get('/center/{id}/edit', [ProgramController::class, 'editCenter'])->name('editCenter');
Route::put('/center/{id}/update', [ProgramController::class, 'updateCenter'])->name('updateCenter');

Route::get('/program/{id}/edit', [ProgramController::class, 'editFlagshipLocal'])->name('edit.flagshiplocal');
Route::put('/program/{id}/update', [ProgramController::class, 'updateFlagshipLocal'])->name('update.flagshiplocal');

Route::get('/subprogram/{id}/edit', [ProgramController::class, 'editSubprogram'])->name('edit.subprogram');
Route::put('/subprogram/{id}/update', [ProgramController::class, 'updateSubprogram'])->name('update.subprogram');

// Programs/Centers Management Routes (prefix group) - KEEP THIS SEPARATE
Route::prefix('programs')->name('programs.')->group(function () {
    // Display all programs
    Route::get('/', [ProgramController::class, 'index'])->name('index');
    
    // DELETE ROUTE
    Route::delete('/{id}', [ProgramController::class, 'destroy'])->name('destroy');
});

// If you want a specific route for centers only
Route::prefix('centers')->name('centers.')->group(function () {
    Route::get('/', [ProgramController::class, 'index'])->name('index')
        ->defaults('type', 'Center');
    
    Route::get('/create', [ProgramController::class, 'create'])->name('create')
        ->defaults('type', 'Center');
});

// If you want a specific route for sub-programs
Route::prefix('sub-programs')->name('sub-programs.')->group(function () {
    Route::get('/', [ProgramController::class, 'index'])->name('index')
        ->defaults('program_type', 'Sub-Program');
});


// PROJECTS ROUTES
Route::prefix('projects')->name('projects.')->group(function () {
    // Index and trash
    Route::get('/', [ProjectController::class, 'index'])->name('index');
    Route::get('/trash', [ProjectController::class, 'trash'])->name('trash');
    
    // Create and store
    Route::get('/create', [ProjectController::class, 'create'])->name('create');
    Route::post('/', [ProjectController::class, 'store'])->name('store');
    
    // Edit and update
    Route::get('/{id}/edit', [ProjectController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ProjectController::class, 'update'])->name('update');
    
    // Delete/restore routes
    Route::delete('/{id}', [ProjectController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/soft-delete', [ProjectController::class, 'softDelete'])->name('softDelete');
    Route::post('/{id}/restore', [ProjectController::class, 'restore'])->name('restore');
    Route::delete('/{id}/force-delete', [ProjectController::class, 'forceDelete'])->name('forceDelete');
    
    // AJAX routes
    Route::get('/program/{programId}', [ProjectController::class, 'getByProgram'])->name('getByProgram');
});


Route::get('/', function () {
    return redirect()->route('login');
})->name('home');


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
    // DASHBOARD
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // ------------------------------------------------------------------------
// EMPLOYEES MANAGEMENT
// ------------------------------------------------------------------------
Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
    ->prefix('employees')->name('employees.')->group(function () {
        Route::get('/', [EmployeeController::class, 'index'])->name('index');
        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->get('/create', [EmployeeController::class, 'create'])->name('create');
        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->post('/', [EmployeeController::class, 'store'])->name('store');
        Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->put('/{employee}', [EmployeeController::class, 'update'])->name('update');
        Route::middleware(['hasPermission:Users.delete,Users.manage,Users.full'])
            ->delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->put('/{employee}/activate', [EmployeeController::class, 'activate'])->name('activate');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->put('/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('deactivate');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->put('/{employee}/restore', [EmployeeController::class, 'restore'])->name('restore');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->delete('/{employee}/force-delete', [EmployeeController::class, 'forceDelete'])->name('force-delete');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->put('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/trashed', [EmployeeController::class, 'trashed'])->name('trashed');
        
        // Utility routes
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/search', [EmployeeController::class, 'search'])->name('search');
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/export', [EmployeeController::class, 'export'])->name('export');
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->post('/import', [EmployeeController::class, 'import'])->name('import');
    });
  // ------------------------------------------------------------------------
    // ROLES MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Users.manage,Users.full']) // Using Users module
        ->prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->get('/create', [RoleController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->post('/', [RoleController::class, 'store'])->name('store');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
            Route::middleware(['hasPermission:Users.manage,Users.full'])
                ->post('/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');
        });
    
    // ------------------------------------------------------------------------
    // MODULE ACCESS MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:module_access.view,module_access.manage,module_access.full'])
        ->prefix('module-access')->name('module-access.')->group(function () {
            Route::get('/', [ModuleAccessController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:module_access.create,module_access.manage,module_access.full'])
                ->post('/', [ModuleAccessController::class, 'store'])->name('store');
            Route::middleware(['hasPermission:module_access.edit,module_access.manage,module_access.full'])
                ->get('/{moduleAccess}/edit', [ModuleAccessController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:module_access.edit,module_access.manage,module_access.full'])
                ->put('/{moduleAccess}', [ModuleAccessController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:module_access.delete,module_access.manage,module_access.full'])
                ->delete('/{moduleAccess}', [ModuleAccessController::class, 'destroy'])->name('destroy');
        });
    
    // ------------------------------------------------------------------------
    // CREDENTIALS MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:employees.manage,employees.full'])
        ->prefix('credentials')->name('credentials.')->group(function () {
            Route::get('/{employee}/edit', [CredentialsEmployeeController::class, 'edit'])->name('edit');
            Route::put('/{employee}', [CredentialsEmployeeController::class, 'update'])->name('update');
            Route::put('/{employee}/reset-password', [CredentialsEmployeeController::class, 'resetPassword'])->name('reset-password');
            Route::put('/{employee}/toggle-status', [CredentialsEmployeeController::class, 'toggleStatus'])->name('toggle-status');
        });
   
    // ------------------------------------------------------------------------
// USERS MODULE
// ------------------------------------------------------------------------
Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
    ->prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        
        // CREATE ROUTES
        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->get('/create', [UserController::class, 'create'])->name('create');
        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->post('/', [UserController::class, 'store'])->name('store');
        
        // IMPORT ROUTES - MUST BE BEFORE {user_id} ROUTES
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->get('/import', [UserController::class, 'importForm'])->name('import.form'); // SHOW FORM
        
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->post('/import', [UserController::class, 'import'])->name('import');
        
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
        
        // EXPORT & STATISTICS
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/export', [UserController::class, 'exportExcel'])->name('export.excel');
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/statistics', [UserController::class, 'statistics'])->name('statistics');
        
        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk.destroy');
        
        // PARAMETERIZED ROUTES - MUST BE LAST
        Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])
            ->get('/{user_id}', [UserController::class, 'show'])->name('show');
        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->get('/{user_id}/edit', [UserController::class, 'edit'])->name('edit');
        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->put('/{user_id}', [UserController::class, 'update'])->name('update');
        Route::middleware(['hasPermission:Users.delete,Users.manage,Users.full'])
            ->delete('/{user_id}', [UserController::class, 'destroy'])->name('destroy');
    });
      // ------------------------------------------------------------------------
    // PORTFOLIOS MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Portfolios.view,Portfolios.manage,Portfolios.full'])
        ->prefix('portfolios')->name('portfolios.')->group(function () {
            Route::get('/', [PortfolioController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:Portfolios.create,Portfolios.manage,Portfolios.full'])
                ->get('/create', [PortfolioController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:Portfolios.create,Portfolios.manage,Portfolios.full'])
                ->post('/', [PortfolioController::class, 'store'])->name('store');
            Route::middleware(['hasPermission:Portfolios.view,Portfolios.manage,Portfolios.full'])
                ->get('/{portfolio}', [PortfolioController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:Portfolios.edit,Portfolios.manage,Portfolios.full'])
                ->get('/{portfolio}/edit', [PortfolioController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:Portfolios.edit,Portfolios.manage,Portfolios.full'])
                ->put('/{portfolio}', [PortfolioController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:Portfolios.delete,Portfolios.manage,Portfolios.full'])
                ->delete('/{portfolio}', [PortfolioController::class, 'destroy'])->name('destroy');
        });
    
    // ------------------------------------------------------------------------
    // COPS MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:COPs.view,COPs.manage,COPs.full'])
        ->prefix('cops')->name('cops.')->group(function () {
            Route::get('/', [CopController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:COPs.create,COPs.manage,COPs.full'])
                ->get('/create', [CopController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:COPs.create,COPs.manage,COPs.full'])
                ->post('/', [CopController::class, 'store'])->name('store');
            Route::middleware(['hasPermission:COPs.view,COPs.manage,COPs.full'])
                ->get('/{cop}', [CopController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:COPs.edit,COPs.manage,COPs.full'])
                ->get('/{cop}/edit', [CopController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:COPs.edit,COPs.manage,COPs.full'])
                ->put('/{cop}', [CopController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:COPs.delete,COPs.manage,COPs.full'])
                ->delete('/{cop}', [CopController::class, 'destroy'])->name('destroy');
        });



    // ------------------------------------------------------------------------
    // ACTIVITIES MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
        ->prefix('activities')->name('activities.')->group(function () {
            Route::get('/', [ActivityController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:activities.create,activities.manage,activities.full'])
                ->get('/create', [ActivityController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:activities.create,activities.manage,activities.full'])
                ->post('/', [ActivityController::class, 'store'])->name('store');
            Route::get('/{activity}', [ActivityController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:activities.edit,activities.manage,activities.full'])
                ->get('/{activity}/edit', [ActivityController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:activities.edit,activities.manage,activities.full'])
                ->put('/{activity}', [ActivityController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:activities.delete,activities.manage,activities.full'])
                ->delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
            Route::middleware(['hasPermission:activities.manage,activities.full'])
                ->delete('/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('bulk.destroy');
            
            Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                ->prefix('{parentActivity}/children')->name('children.')->group(function () {
                    Route::get('/', [ActivityController::class, 'indexChildren'])->name('index');
                    Route::middleware(['hasPermission:activities.create,activities.manage,activities.full'])
                        ->get('/create', [ActivityController::class, 'createChild'])->name('create');
                    Route::middleware(['hasPermission:activities.create,activities.manage,activities.full'])
                        ->post('/', [ActivityController::class, 'storeChild'])->name('store');
                });
            
            // AJAX routes for activities
            Route::prefix('ajax')->group(function () {
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/get-rp-activities', [ActivityController::class, 'getRPActivities'])->name('get-rp-activities');
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])->name('get-rp-actions-with-activities');
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])->name('get-projects-by-program');
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/get-action-plans', [ActivityController::class, 'getActionPlans'])->name('get-action-plans');
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/get-components-by-action-plan', [ActivityController::class, 'getComponentsByActionPlan'])->name('get-components-by-action-plan');
                Route::middleware(['hasPermission:activities.view,activities.manage,activities.full'])
                    ->get('/get-rp-components', [ActivityController::class, 'getRPComponents'])->name('get-rp-components');
            });
        });
    
    // ------------------------------------------------------------------------
    // ACTION PLANS MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Reports.view,Reports.create,Reports.full']) // Using Reports module
        ->prefix('action-plans')->name('action-plans.')->group(function () {
            Route::get('/', [ActionPlanController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:Reports.full'])
                ->delete('/bulk-destroy', [ActionPlanController::class, 'bulkDestroy'])->name('bulk.destroy');
            Route::middleware(['hasPermission:Reports.full'])
                ->delete('/{id}', [ActionPlanController::class, 'destroy'])->name('destroy');
            Route::middleware(['hasPermission:Reports.view,Reports.create,Reports.full'])
                ->get('/{actionPlan}/download', [ActionPlanController::class, 'download'])->name('download');
        });
    
    // ------------------------------------------------------------------------
    // REPORTING MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:reports.view,reports.create,reports.full'])
        ->prefix('reporting')->name('reporting.')->group(function () {
            Route::get('/import', [ReportingImportController::class, 'index'])->name('import.import');
            Route::middleware(['hasPermission:reports.create,reports.full'])
                ->post('/import', [ReportingImportController::class, 'import'])->name('import.process');
            Route::middleware(['hasPermission:reports.view,reports.create,reports.full'])
                ->post('/import/preview', [ReportingImportController::class, 'preview'])->name('import.preview');
            Route::middleware(['hasPermission:reports.view,reports.create,reports.full'])
                ->get('/import/template', [ReportingImportController::class, 'downloadTemplate'])->name('import.download-template');
            Route::middleware(['hasPermission:reports.create,reports.full'])
                ->post('/reporting/import/process', [ReportingImportController::class, 'process'])->name('reporting.import.process');
        });
    
   
    
    // ------------------------------------------------------------------------
    // PROGRAMS MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:programs.view,programs.manage,programs.full'])
        ->prefix('programs')->name('programs.')->group(function () {
            Route::get('/', [ProgramController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:programs.create,programs.manage,programs.full'])
                ->get('/create', [ProgramController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:programs.create,programs.manage,programs.full'])
                ->post('/', [ProgramController::class, 'store'])->name('store');
            Route::get('/{program}', [ProgramController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:programs.edit,programs.manage,programs.full'])
                ->get('/{program}/edit', [ProgramController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:programs.edit,programs.manage,programs.full'])
                ->put('/{program}', [ProgramController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:programs.delete,programs.manage,programs.full'])
                ->delete('/{program}', [ProgramController::class, 'destroy'])->name('destroy');
        });
    
    // ------------------------------------------------------------------------
    // PROJECTS MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:projects.view,projects.manage,projects.full'])
        ->prefix('projects')->name('projects.')->group(function () {
            Route::get('/', [ProjectController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:projects.create,projects.manage,projects.full'])
                ->get('/create', [ProjectController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:projects.create,projects.manage,projects.full'])
                ->post('/', [ProjectController::class, 'store'])->name('store');
            Route::get('/{project}', [ProjectController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:projects.edit,projects.manage,projects.full'])
                ->get('/{project}/edit', [ProjectController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:projects.edit,projects.manage,projects.full'])
                ->put('/{project}', [ProjectController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:projects.delete,projects.manage,projects.full'])
                ->delete('/{project}', [ProjectController::class, 'destroy'])->name('destroy');
            
            // Additional project-specific routes
            Route::get('/{project}/activities', [ProjectController::class, 'activities'])->name('activities');
            Route::get('/{project}/reports', [ProjectController::class, 'reports'])->name('reports');
            Route::get('/{project}/budget', [ProjectController::class, 'budget'])->name('budget');
        });
    
}); // End of auth middleware group