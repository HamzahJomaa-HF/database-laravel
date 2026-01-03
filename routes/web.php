<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reporting\{
  
    ReportingImportController
};
use App\Http\Controllers\API\UserController as APIUserController;


Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [APIUserController::class, 'index'])->name('index');
    Route::post('/', [APIUserController::class, 'store'])->name('store');
    Route::get('/create', [APIUserController::class, 'create'])->name('create');
    Route::get('/statistics', [APIUserController::class, 'statistics'])->name('statistics');
    Route::get('/import', [APIUserController::class, 'importForm'])->name('import.form');
Route::post('/import', [APIUserController::class, 'import'])->name('import');    Route::get('/import/template', [APIUserController::class, 'downloadTemplate'])->name('import.template');
    Route::get('/export', [APIUserController::class, 'exportExcel'])->name('export.excel');
     // Bulk delete route - ADD THIS
    Route::post('/bulk-delete', [APIUserController::class, 'bulkDestroy'])->name('bulk.destroy');
    
    // Add these missing routes:
    Route::get('/{user_id}/edit', [APIUserController::class, 'edit'])->name('edit');
    Route::put('/{user_id}', [APIUserController::class, 'update'])->name('update');
    Route::delete('/{user_id}', [APIUserController::class, 'destroy'])->name('destroy');
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