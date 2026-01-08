<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reporting\{
  
    ReportingImportController
};
use App\Http\Controllers\UserController as UserController;
use App\Http\Controllers\ActionPlanController;

// In your routes file
Route::get('/actionPlans', [ActionPlanController::class, 'index'])->name('action-plans.index');
Route::delete('/action-plans/bulk-destroy', [ActionPlanController::class, 'bulkDestroy'])->name('action-plans.bulk.destroy');
Route::delete('/action-plans/{id}', [ActionPlanController::class, 'destroy'])->name('action-plans.destroy');
// In your routes file, add this line:
Route::get('/action-plans/{actionPlan}/download', [ActionPlanController::class, 'download'])->name('action-plans.download');


//routes for storage setup and file listing
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

//users routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
    Route::get('/import', [UserController::class, 'importForm'])->name('import.form');
    Route::post('/import', [UserController::class, 'import'])->name('import');
    Route::get('/import/template', [UserController::class, 'downloadTemplate'])->name('import.template');
    Route::get('/export', [UserController::class, 'exportExcel'])->name('export.excel');
     // Bulk delete route - ADD THIS
    Route::post('/bulk-delete', [UserController::class, 'bulkDestroy'])->name('bulk.destroy');
    
    // Add these missing routes:
    Route::get('/{user_id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{user_id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{user_id}', [UserController::class, 'destroy'])->name('destroy');
});

// Root
Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\ActivityController;

Route::prefix('activities')->name('activities.')->group(function () {

    /*
    |--------------------------------------------------------------
    | Collection Routes
    |--------------------------------------------------------------
    */
    Route::get('/', [ActivityController::class, 'index'])
        ->name('index');

    Route::get('/create', [ActivityController::class, 'create'])
        ->name('create');

    Route::post('/', [ActivityController::class, 'store'])
        ->name('store');

    Route::delete('/bulk-destroy', [ActivityController::class, 'bulkDestroy'])
        ->name('bulk.destroy');

    /*
    |--------------------------------------------------------------
    | Helper / AJAX Routes
    |--------------------------------------------------------------
    */
    Route::get('/get-rp-activities', [ActivityController::class, 'getRPActivities'])
        ->name('get-rp-activities');

    Route::get('/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])
        ->name('get-rp-actions-with-activities');

    Route::get('/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])
        ->name('get-projects-by-program');

    // ============================================
    // ADD THESE 3 ACTION PLAN ROUTES HERE
    // ============================================
    Route::get('/get-action-plans', [ActivityController::class, 'getActionPlans'])
        ->name('get-action-plans');
        
    Route::get('/get-components-by-action-plan', [ActivityController::class, 'getComponentsByActionPlan'])
        ->name('get-components-by-action-plan');
        
    Route::get('/get-rp-components', [ActivityController::class, 'getRPComponents'])
        ->name('get-rp-components');
    // ============================================

    /*
    |--------------------------------------------------------------
    | Child Activities (Nested under Parent Activity)
    |--------------------------------------------------------------
    */
    Route::prefix('{parentActivity}/children')->name('children.')->group(function () {

        Route::get('/', [ActivityController::class, 'indexChildren'])
            ->name('index');

        Route::get('/create', [ActivityController::class, 'createChild'])
            ->name('create');

        Route::post('/', [ActivityController::class, 'storeChild'])
            ->name('store');
    });

    /*
    |--------------------------------------------------------------
    | Single Activity Routes (MUST BE LAST)
    |--------------------------------------------------------------
    */
    Route::get('/{activity}', [ActivityController::class, 'edit'])
        ->name('edit');

    Route::put('/{activity}', [ActivityController::class, 'update'])
        ->name('update');

    Route::delete('/{activity}', [ActivityController::class, 'destroy'])
        ->name('destroy');
});

// =======================
Route::prefix('reporting')->name('reporting.')->group(function () {

    // -----------------------
    // Import Routes
    // -----------------------
    Route::get('/import', [ReportingImportController::class, 'index'])->name('import.import');
    Route::post('/import', [ReportingImportController::class, 'import'])->name('import.process');
    
    Route::post('/import/preview', [ReportingImportController::class, 'preview'])->name('import.preview');
    Route::get('/import/template', [ReportingImportController::class, 'downloadTemplate'])->name('import.download-template');
    Route::post('/reporting/import/process', [ReportingImportController::class, 'process'])->name('reporting.import.process');
});