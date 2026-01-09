<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reporting\ReportingImportController;
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\ActionPlanController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;

// =============== PUBLIC ROUTES (Keep as they are) ===============

// Root
Route::get('/', function () {
    return view('welcome');
});

// Action Plans Routes
Route::get('/actionPlans', [ActionPlanController::class, 'index'])->name('action-plans.index');
Route::delete('/action-plans/bulk-destroy', [ActionPlanController::class, 'bulkDestroy'])->name('action-plans.bulk.destroy');
Route::delete('/action-plans/{id}', [ActionPlanController::class, 'destroy'])->name('action-plans.destroy');
Route::get('/action-plans/{actionPlan}/download', [ActionPlanController::class, 'download'])->name('action-plans.download');

// Storage setup utilities (public for debugging)
Route::get('/setup-storage', function () {
    echo "<h1>Setting up storage directories</h1>";
    
    $directories = [
        'action-plans',
        'public/action-plans',
        'uploads',
    ];
    
    foreach ($directories as $dir) {
        $fullPath = storage_path('app/' . $dir);
        
        if (!file_exists($fullPath)) {
            mkdir($fullPath, 0755, true);
            echo "✅ Created: {$fullPath}<br>";
        } else {
            echo "✓ Already exists: {$fullPath}<br>";
        }
    }
    
    echo "<br><strong>All directories created successfully!</strong>";
    
    // Also create symbolic link for public storage
    echo "<br><br><h2>Creating symbolic link...</h2>";
    if (!file_exists(public_path('storage'))) {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        echo "✅ Symbolic link created";
    } else {
        echo "✓ Symbolic link already exists";
    }
});

Route::get('/list-files-in-storage', function () {
    $directories = [
        'action-plans',
        'public/action-plans', 
        'uploads'
    ];
    
    echo "<h1>Current Files in Storage</h1>";
    
    foreach ($directories as $dir) {
        $fullPath = storage_path('app/' . $dir);
        echo "<h3>Directory: {$dir}</h3>";
        
        if (!is_dir($fullPath)) {
            echo "❌ Directory doesn't exist<br>";
            continue;
        }
        
        $files = scandir($fullPath);
        $validFiles = array_filter($files, function($file) {
            return $file !== '.' && $file !== '..';
        });
        
        if (empty($validFiles)) {
            echo "📁 Empty directory<br>";
        } else {
            echo "<ul>";
            foreach ($validFiles as $file) {
                $filePath = $fullPath . '/' . $file;
                $size = filesize($filePath);
                $type = is_dir($filePath) ? '📁 Directory' : '📄 File';
                echo "<li>{$type}: {$file} ({$size} bytes)</li>";
            }
            echo "</ul>";
        }
        echo "<hr>";
    }
});

// Users Routes (PUBLIC - Keep as is)
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
    Route::get('/import', [UserController::class, 'importForm'])->name('import.form');
    Route::post('/import', [UserController::class, 'import'])->name('import');
    Route::get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
    Route::get('/export', [UserController::class, 'exportExcel'])->name('export.excel');
    Route::post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk.destroy');
    
    Route::get('/{user_id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user_id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user_id}', [UserController::class, 'destroy'])->name('destroy');
});

// Activities Routes (PUBLIC - Keep as is)
Route::prefix('activities')->name('activities.')->group(function () {
    Route::get('/', [ActivityController::class, 'index'])->name('index');
    Route::get('/create', [ActivityController::class, 'create'])->name('create');
    Route::post('/', [ActivityController::class, 'store'])->name('store');
    Route::delete('/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('bulk.destroy');

    // Helper / AJAX Routes
    Route::get('/get-rp-activities', [ActivityController::class, 'getRPActivities'])->name('get-rp-activities');
    Route::get('/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])->name('get-rp-actions-with-activities');
    Route::get('/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])->name('get-projects-by-program');
    Route::get('/get-action-plans', [ActivityController::class, 'getActionPlans'])->name('get-action-plans');
    Route::get('/get-components-by-action-plan', [ActivityController::class, 'getComponentsByActionPlan'])->name('get-components-by-action-plan');
    Route::get('/get-rp-components', [ActivityController::class, 'getRPComponents'])->name('get-rp-components');

    // Child Activities
    Route::prefix('{parentActivity}/children')->name('children.')->group(function () {
        Route::get('/', [ActivityController::class, 'indexChildren'])->name('index');
        Route::get('/create', [ActivityController::class, 'createChild'])->name('create');
        Route::post('/', [ActivityController::class, 'storeChild'])->name('store');
    });

    // Single Activity Routes
    Route::get('/{activity}', [ActivityController::class, 'edit'])->name('edit');
    Route::put('/{activity}', [ActivityController::class, 'update'])->name('update');
    Route::delete('/{activity}', [ActivityController::class, 'destroy'])->name('destroy');
});

// Reporting Routes (PUBLIC - Keep as is)
Route::prefix('reporting')->name('reporting.')->group(function () {
    Route::get('/import', [ReportingImportController::class, 'index'])->name('import.import');
    Route::post('/import', [ReportingImportController::class, 'import'])->name('import.process');
    Route::post('/import/preview', [ReportingImportController::class, 'preview'])->name('import.preview');
    Route::get('/import/template', [ReportingImportController::class, 'downloadTemplate'])->name('import.download-template');
    Route::post('/reporting/import/process', [ReportingImportController::class, 'process'])->name('reporting.import.process');
});

// =============== AUTHENTICATION ROUTES ===============
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// =============== PROTECTED ROUTES (Authentication Required) ===============
Route::middleware(['auth:employee'])->group(function () {
    
    // Dashboard (accessible to all authenticated employees)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // =============== PROTECTED VERSIONS OF EXISTING ROUTES ===============
    // You can add protected versions here if needed, for example:
    
    // Protected Activities Module (with module access)
    Route::middleware(['module.access:activities,view'])
         ->prefix('protected/activities')
         ->name('protected.activities.')
         ->group(function () {
             
             Route::get('/', [ActivityController::class, 'index'])->name('index');
             
             Route::middleware(['module.access:activities,create'])->group(function () {
                 Route::get('/create', [ActivityController::class, 'create'])->name('create');
                 Route::post('/', [ActivityController::class, 'store'])->name('store');
             });
             
             Route::prefix('{activity}')->group(function () {
                 Route::get('/', [ActivityController::class, 'show'])
                      ->middleware(['resource.access:view'])
                      ->name('show');
                 
                 Route::middleware(['resource.access:edit'])->group(function () {
                     Route::get('/edit', [ActivityController::class, 'edit'])->name('edit');
                     Route::put('/', [ActivityController::class, 'update'])->name('update');
                 });
                 
                 Route::delete('/', [ActivityController::class, 'destroy'])
                      ->middleware(['resource.access:delete', 'permission:delete-activities'])
                      ->name('destroy');
             });
         });
    
    // Protected Users Module (with module access)
    Route::middleware(['module.access:users,view'])
         ->prefix('protected/users')
         ->name('protected.users.')
         ->group(function () {
             
             Route::get('/', [UserController::class, 'index'])->name('index');
             
             Route::middleware(['module.access:users,create', 'permission:create-users'])->group(function () {
                 Route::get('/create', [UserController::class, 'create'])->name('create');
                 Route::post('/', [UserController::class, 'store'])->name('store');
             });
             
             Route::prefix('{user}')->group(function () {
                 Route::get('/', [UserController::class, 'show'])
                      ->middleware(['resource.access:view'])
                      ->name('show');
                 
                 Route::middleware(['resource.access:edit', 'permission:edit-users'])->group(function () {
                     Route::get('/edit', [UserController::class, 'edit'])->name('edit');
                     Route::put('/', [UserController::class, 'update'])->name('update');
                 });
             });
         });
    
    // Protected Reporting Module (with module access)
    Route::middleware(['module.access:reports,view'])
         ->prefix('protected/reporting')
         ->name('protected.reporting.')
         ->group(function () {
             
             Route::get('/import', [ReportingImportController::class, 'index'])->name('import.import');
             Route::post('/import', [ReportingImportController::class, 'import'])->name('import.process');
             
             // Add other reporting routes as needed
         });
});