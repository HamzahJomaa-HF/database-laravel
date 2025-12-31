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

use App\Http\Controllers\API\ActivityController;

// SPECIFIC ROUTES FIRST
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
Route::delete('/activities/bulk-destroy', [ActivityController::class, 'bulkDestroy'])->name('activities.bulk.destroy');
Route::get('/activities/get-rp-activities', [ActivityController::class, 'getRPActivities'])->name('activities.get-rp-activities');
// Add this line with your other specific routes
Route::get('/activities/rp-actions', [ActivityController::class, 'getRPActionsWithActivities'])
     ->name('activities.get-rp-actions-with-activities');
     // Add this route
Route::get('/activities/get-projects-by-program', [ActivityController::class, 'getProjectsByProgram'])
    ->name('activities.get-projects-by-program');
Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
//                                                       
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