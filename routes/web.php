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
use App\Http\Controllers\ActivityUserController;

use Illuminate\Support\Facades\DB;

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

Route::get('/', function () {
    return view('welcome');
})->middleware('auth')->name('home');

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
    
    // ============================================================================
    // PROGRAMS/CENTERS MANAGEMENT ROUTES WITH PERMISSIONS
    // ============================================================================

    // CREATE ROUTES - Require create or manage or full permissions
    Route::middleware(['hasPermission:programs.create,programs.manage,programs.full'])
        ->group(function () {
            Route::get('/center/create', [ProgramController::class, 'createCenter'])->name('createCenter');
            Route::get('/program/create', [ProgramController::class, 'createFlagshipLocal'])->name('create.flagshiplocal');
            Route::get('/subprogram/create', [ProgramController::class, 'createSubprogram'])->name('create.subprogram');
        });

    // STORE ROUTES (POST) - Require create or manage or full permissions
    Route::middleware(['hasPermission:programs.create,programs.manage,programs.full'])
        ->group(function () {
            Route::post('/center/create', [ProgramController::class, 'storeCenter'])->name('storeCenter');
            Route::post('/program/create', [ProgramController::class, 'storeFlagshipLocal'])->name('storeFlagshipLocal');
            Route::post('/subprogram/create', [ProgramController::class, 'storeSubprogram'])->name('storeSubprogram');
        });

    // EDIT ROUTES - Require edit or manage or full permissions
    Route::middleware(['hasPermission:programs.edit,programs.manage,programs.full'])
        ->group(function () {
            Route::get('/center/{id}/edit', [ProgramController::class, 'editCenter'])->name('editCenter');
            Route::put('/center/{id}/update', [ProgramController::class, 'updateCenter'])->name('updateCenter');
            Route::get('/program/{id}/edit', [ProgramController::class, 'editFlagshipLocal'])->name('edit.flagshiplocal');
            Route::put('/program/{id}/update', [ProgramController::class, 'updateFlagshipLocal'])->name('update.flagshiplocal');
            Route::get('/subprogram/{id}/edit', [ProgramController::class, 'editSubprogram'])->name('edit.subprogram');
            Route::put('/subprogram/{id}/update', [ProgramController::class, 'updateSubprogram'])->name('update.subprogram');
        });

    // Programs/Centers Management Routes
    Route::prefix('programs')->name('programs.')->group(function () {
        // View all programs - Requires view, manage, or full
        Route::middleware(['hasPermission:programs.view,programs.manage,programs.full'])
            ->get('/', [ProgramController::class, 'index'])->name('index');
        
        // Delete program - Requires delete, manage, or full
        Route::middleware(['hasPermission:programs.delete,programs.manage,programs.full'])
            ->delete('/{id}', [ProgramController::class, 'destroy'])->name('destroy');
    });

    // Centers specific routes
    Route::prefix('centers')->name('centers.')->group(function () {
        // View centers - Requires view, manage, or full
        Route::middleware(['hasPermission:programs.view,programs.manage,programs.full'])
            ->get('/', [ProgramController::class, 'index'])->name('index')
            ->defaults('type', 'Center');
        
        // Create center - Requires create, manage, or full
        Route::middleware(['hasPermission:programs.create,programs.manage,programs.full'])
            ->get('/create', [ProgramController::class, 'create'])->name('create')
            ->defaults('type', 'Center');
    });

    // Sub-programs specific routes
    Route::prefix('sub-programs')->name('sub-programs.')->group(function () {
        // View sub-programs - Requires view, manage, or full
        Route::middleware(['hasPermission:programs.view,programs.manage,programs.full'])
            ->get('/', [ProgramController::class, 'index'])->name('index')
            ->defaults('program_type', 'Sub-Program');
    });

    // ------------------------------------------------------------------------
    // EMPLOYEES MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Employees.view,Employees.manage,Employees.full'])
        ->prefix('employees')->name('employees.')->group(function () {
            Route::get('/', [EmployeeController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:Employees.create,Employees.manage,Employees.full'])
                ->get('/create', [EmployeeController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:Employees.create,Employees.manage,Employees.full'])
                ->post('/', [EmployeeController::class, 'store'])->name('store');
            Route::get('/{employee}', [EmployeeController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:Employees.edit,Employees.manage,Employees.full'])
                ->get('/{employee}/edit', [EmployeeController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:Employees.edit,Employees.manage,Employees.full'])
                ->put('/{employee}', [EmployeeController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:Employees.delete,Employees.manage,Employees.full'])
                ->delete('/{employee}', [EmployeeController::class, 'destroy'])->name('destroy');
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->put('/{employee}/activate', [EmployeeController::class, 'activate'])->name('activate');
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->put('/{employee}/deactivate', [EmployeeController::class, 'deactivate'])->name('deactivate');
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->put('/{employee}/restore', [EmployeeController::class, 'restore'])->name('restore');
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->delete('/{employee}/force-delete', [EmployeeController::class, 'forceDelete'])->name('force-delete');
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->put('/{employee}/toggle-status', [EmployeeController::class, 'toggleStatus'])->name('toggle-status');
            Route::get('/trashed', [EmployeeController::class, 'trashed'])->name('trashed');
            
            // Utility routes
            Route::middleware(['hasPermission:Employees.view,Employees.manage,Employees.full'])
                ->get('/search', [EmployeeController::class, 'search'])->name('search');
            
            // Export route - FIXED: Using export permission
            Route::middleware(['hasPermission:Employees.export,Employees.full'])
                ->get('/export', [EmployeeController::class, 'export'])->name('export');
            
            Route::middleware(['hasPermission:Employees.manage,Employees.full'])
                ->post('/import', [EmployeeController::class, 'import'])->name('import');
        });

    // ------------------------------------------------------------------------
    // ROLES MANAGEMENT
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Roles.manage,Roles.full'])
        ->prefix('roles')->name('roles.')->group(function () {
            Route::get('/', [RoleController::class, 'index'])->name('index');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->get('/create', [RoleController::class, 'create'])->name('create');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->post('/', [RoleController::class, 'store'])->name('store');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->get('/{role}', [RoleController::class, 'show'])->name('show');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->get('/{role}/edit', [RoleController::class, 'edit'])->name('edit');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->put('/{role}', [RoleController::class, 'update'])->name('update');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->delete('/{role}', [RoleController::class, 'destroy'])->name('destroy');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
                ->get('/{role}/permissions', [RoleController::class, 'permissions'])->name('permissions');
            Route::middleware(['hasPermission:Roles.manage,Roles.full'])
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
Route::prefix('users')->name('users.')->group(function () {

    // EXPORT ROUTE - separated from manage/edit/delete
    Route::middleware(['hasPermission:Users.export,Users.full'])
        ->get('/export', [UserController::class, 'exportExcel'])
        ->name('export.excel');

    Route::middleware(['hasPermission:Users.view,Users.manage,Users.full'])->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');

        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->get('/create', [UserController::class, 'create'])->name('create');

        Route::middleware(['hasPermission:Users.create,Users.manage,Users.full'])
            ->post('/', [UserController::class, 'store'])->name('store');

        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->get('/import', [UserController::class, 'importForm'])->name('import.form');

        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->post('/import', [UserController::class, 'import'])->name('import');

        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');

        Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');

        Route::middleware(['hasPermission:Users.manage,Users.full'])
            ->post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk.destroy');

        Route::get('/{user_id}', [UserController::class, 'show'])->name('show');

        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->get('/{user_id}/edit', [UserController::class, 'edit'])->name('edit');

        Route::middleware(['hasPermission:Users.edit,Users.manage,Users.full'])
            ->put('/{user_id}', [UserController::class, 'update'])->name('update');

        Route::middleware(['hasPermission:Users.delete,Users.manage,Users.full'])
            ->delete('/{user_id}', [UserController::class, 'destroy'])->name('destroy');
    });
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
Route::prefix('activities')->name('activities.')->group(function () {

    // EXPORT ROUTE - separated from manage/edit/delete
    Route::middleware(['hasPermission:Activities.export,Activities.full'])
        ->get('/export', [ActivityController::class, 'export'])
        ->name('export');

    Route::middleware(['hasPermission:Activities.view,Activities.manage,Activities.full'])->group(function () {

        Route::middleware(['hasPermission:Activities.manage,Activities.full'])
            ->get('/import', [ActivityController::class, 'showImportForm'])->name('import');

        Route::middleware(['hasPermission:Activities.manage,Activities.full'])
            ->post('/import', [ActivityController::class, 'import'])->name('import.store');

        Route::middleware(['hasPermission:Activities.manage,Activities.full'])
            ->get('/import/template', [ActivityController::class, 'downloadTemplate'])->name('import.template');

        Route::get('/', [ActivityController::class, 'index'])->name('index');

        Route::middleware(['hasPermission:Activities.create,Activities.manage,Activities.full'])
            ->get('/create', [ActivityController::class, 'create'])->name('create');

        Route::middleware(['hasPermission:Activities.create,Activities.manage,Activities.full'])
            ->post('/', [ActivityController::class, 'store'])->name('store');

        Route::get('/{activity}', [ActivityController::class, 'show'])->name('show');

        Route::middleware(['hasPermission:Activities.edit,Activities.manage,Activities.full'])
            ->get('/{activity}/edit', [ActivityController::class, 'edit'])->name('edit');

        Route::middleware(['hasPermission:Activities.edit,Activities.manage,Activities.full'])
            ->put('/{activity}', [ActivityController::class, 'update'])->name('update');

        Route::middleware(['hasPermission:Activities.delete,Activities.manage,Activities.full'])
            ->delete('/bulk/destroy', [ActivityController::class, 'bulkDestroy'])->name('bulk.destroy');

        Route::middleware(['hasPermission:Activities.delete,Activities.manage,Activities.full'])
            ->delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');

        Route::prefix('{parentActivity}/children')->name('children.')->group(function () {
            Route::get('/', [ActivityController::class, 'indexChildren'])->name('index');

            Route::middleware(['hasPermission:Activities.create,Activities.manage,Activities.full'])
                ->get('/create', [ActivityController::class, 'createChild'])->name('create');

            Route::middleware(['hasPermission:Activities.create,Activities.manage,Activities.full'])
                ->post('/', [ActivityController::class, 'storeChild'])->name('store');
        });

        Route::prefix('ajax')->group(function () {
            Route::get('/get-rp-activities', [ActivityController::class, 'getRPActivities'])->name('get-rp-activities');
            Route::get('/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])->name('get-rp-actions-with-activities');
            Route::get('/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])->name('get-projects-by-program');
            Route::get('/get-action-plans', [ActivityController::class, 'getActionPlans'])->name('get-action-plans');
            Route::get('/get-components-by-action-plan', [ActivityController::class, 'getComponentsByActionPlan'])->name('get-components-by-action-plan');
            Route::get('/get-rp-components', [ActivityController::class, 'getRPComponents'])->name('get-rp-components');
        });
    });
});
    
    // ------------------------------------------------------------------------
    // ACTION PLANS MODULE
    // ------------------------------------------------------------------------
    Route::middleware(['hasPermission:Reports.view,Reports.create,Reports.full'])
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

    // ------------------------------------------------------------------------
// ACTIVITY USERS MODULE
// ------------------------------------------------------------------------
Route::prefix('activity-users')->name('activity-users.')->group(function () {

    // EXPORT ROUTE - separated from manage/edit/delete
    Route::middleware(['hasPermission:ActivityUsers.export,ActivityUsers.full'])
        ->get('/export/csv', [ActivityUserController::class, 'export'])
        ->name('export');

    Route::middleware(['hasPermission:ActivityUsers.view,ActivityUsers.manage,ActivityUsers.full'])->group(function () {

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->get('/import', [ActivityUserController::class, 'importForm'])->name('import.form');

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->post('/import', [ActivityUserController::class, 'import'])->name('import.process');

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->get('/download-template', [ActivityUserController::class, 'downloadTemplate'])->name('download-template');

        Route::get('/', [ActivityUserController::class, 'index'])->name('index');

        Route::middleware(['hasPermission:ActivityUsers.create,ActivityUsers.manage,ActivityUsers.full'])
            ->get('/create', [ActivityUserController::class, 'create'])->name('create');

        Route::middleware(['hasPermission:ActivityUsers.create,ActivityUsers.manage,ActivityUsers.full'])
            ->post('/', [ActivityUserController::class, 'store'])->name('store');

        Route::middleware(['hasPermission:ActivityUsers.edit,ActivityUsers.manage,ActivityUsers.full'])
            ->get('/{id}/edit', [ActivityUserController::class, 'edit'])->name('edit');

        Route::middleware(['hasPermission:ActivityUsers.edit,ActivityUsers.manage,ActivityUsers.full'])
            ->put('/{id}', [ActivityUserController::class, 'update'])->name('update');

        Route::middleware(['hasPermission:ActivityUsers.delete,ActivityUsers.manage,ActivityUsers.full'])
            ->delete('/{id}', [ActivityUserController::class, 'destroy'])->name('destroy');

        Route::middleware(['hasPermission:ActivityUsers.delete,ActivityUsers.manage,ActivityUsers.full'])
            ->delete('/bulk/destroy', [ActivityUserController::class, 'bulkDestroy'])->name('bulk.destroy');

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->get('/trash/list', [ActivityUserController::class, 'trash'])->name('trash');

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->post('/{id}/restore', [ActivityUserController::class, 'restore'])->name('restore');

        Route::middleware(['hasPermission:ActivityUsers.manage,ActivityUsers.full'])
            ->delete('/{id}/force-delete', [ActivityUserController::class, 'forceDelete'])->name('force-delete');
    });
});
// Token management
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/api-tokens', [App\Http\Controllers\TokenManagementController::class, 'index'])->name('tokens.index');
        Route::post('/api-tokens/generate', [App\Http\Controllers\TokenManagementController::class, 'generate'])->name('tokens.generate');
        Route::delete('/api-tokens/{tokenId}', [App\Http\Controllers\TokenManagementController::class, 'revoke'])->name('tokens.revoke');
    });
    
    // Simple token endpoint for console
    Route::post('/get-api-token', function () {
        $token = auth()->user()->createToken('console-token')->plainTextToken;
        return response()->json(['token' => $token]);
});
});