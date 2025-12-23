<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reporting\{
  
    ReportingImportController
};
use App\Http\Controllers\API\UserController as APIUserController;
use App\Http\Controllers\API\ActivityController;

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


// =======================
// Reporting Routes Group
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
    
   
    

Route::get('/activities/edit', function () {
    return view('activities.edit');
})->name('activities.edit');

   

    
});