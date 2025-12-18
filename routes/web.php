<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Reporting\{
    ReportingComponentController,
    ReportingProgramController,
    ReportingUnitController,
    ReportingActionController,
    ReportingActivityController,
    ReportingIndicatorController,
    ReportingFocalPointController,
    ReportingTargetActionController,
    ReportingActivityIndicatorController,
    ReportingActivityFocalPointController,
    ReportingMappingController,
    ReportingImportController
};
use App\Http\Controllers\API\UserController as APIUserController;

Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [APIUserController::class, 'index'])->name('index');          // All Users
    Route::get('/create', [APIUserController::class, 'create'])->name('create'); // Add New User
    Route::get('/statistics', [APIUserController::class, 'statistics'])->name('statistics'); 
    Route::get('/import', [APIUserController::class, 'importForm'])->name('import.form');
    Route::post('/import', [APIUserController::class, 'import'])->name('import.process');
    Route::get('/export', [APIUserController::class, 'exportExcel'])->name('export.excel');
});


// Root
Route::get('/', function () {
    return view('welcome');
});
// Users Routes
Route::prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::get('/statistics', [UserController::class, 'statistics'])->name('statistics');
    Route::get('/import', [UserController::class, 'importForm'])->name('import.form');
    Route::post('/import', [UserController::class, 'import'])->name('import.process');
    Route::get('/export', [UserController::class, 'exportExcel'])->name('export.excel');
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
    // -----------------------
    // Main Resources
    // -----------------------
    Route::resource('components', ReportingComponentController::class);
    Route::resource('programs', ReportingProgramController::class);
    Route::resource('units', ReportingUnitController::class);
    Route::resource('actions', ReportingActionController::class);
    Route::resource('activities', ReportingActivityController::class);
    Route::resource('focalpoints', ReportingFocalPointController::class);
    Route::resource('target-actions', ReportingTargetActionController::class);

    // -----------------------
    // Pivot/Join Tables
    // -----------------------
    Route::resource('activity-indicators', ReportingActivityIndicatorController::class);
    Route::resource('activity-focalpoints', ReportingActivityFocalPointController::class);
    Route::resource('mappings', ReportingMappingController::class);

    // -----------------------
    // AJAX / Additional Routes
    // -----------------------

    // Programs
    Route::get('programs/by-component/{componentId}', [ReportingProgramController::class, 'getByComponent'])
        ->name('programs.by-component');
    Route::get('programs/statistics/{id}', [ReportingProgramController::class, 'getStatistics'])
        ->name('programs.statistics');
    Route::post('programs/duplicate/{id}', [ReportingProgramController::class, 'duplicate'])
        ->name('programs.duplicate');

    // Units
    Route::get('units/by-program/{programId}', [ReportingUnitController::class, 'getByProgram'])
        ->name('units.by-program');
    Route::get('units/statistics/{id}', [ReportingUnitController::class, 'getStatistics'])
        ->name('units.statistics');
    Route::get('units/by-type/{type}', [ReportingUnitController::class, 'getByType'])
        ->name('units.by-type');
    Route::post('units/toggle-status/{id}', [ReportingUnitController::class, 'toggleStatus'])
        ->name('units.toggle-status');

    // Actions
    Route::get('actions/by-unit/{unitId}', [ReportingActionController::class, 'getByUnit'])
        ->name('actions.by-unit');
    Route::get('actions/details/{id}', [ReportingActionController::class, 'getDetails'])
        ->name('actions.details');

    // Activities
    Route::get('activities/by-action/{actionId}', [ReportingActivityController::class, 'getByAction'])
        ->name('activities.by-action');
    Route::get('activities/statistics/{id}', [ReportingActivityController::class, 'getStatistics'])
        ->name('activities.statistics');
    Route::post('activities/sync/{id}', [ReportingActivityController::class, 'syncWithMain'])
        ->name('activities.sync');
    Route::post('activities/{id}/attach-indicators', [ReportingActivityController::class, 'attachIndicators'])
        ->name('activities.attach-indicators');
    Route::post('activities/{id}/attach-focalpoints', [ReportingActivityController::class, 'attachFocalpoints'])
        ->name('activities.attach-focalpoints');

    // Focal Points
    Route::get('focalpoints/by-code/{code}', [ReportingFocalPointController::class, 'getByCode'])
        ->name('focalpoints.by-code');
    Route::get('focalpoints/by-department/{department}', [ReportingFocalPointController::class, 'getByDepartment'])
        ->name('focalpoints.by-department');
    Route::post('focalpoints/{id}/toggle-status', [ReportingFocalPointController::class, 'toggleStatus'])
        ->name('focalpoints.toggle-status');
    Route::get('focalpoints/{id}/workload-statistics', [ReportingFocalPointController::class, 'getWorkloadStatistics'])
        ->name('focalpoints.workload-statistics');
    Route::post('focalpoints/import', [ReportingFocalPointController::class, 'import'])
        ->name('focalpoints.import');
    Route::get('focalpoints/export', [ReportingFocalPointController::class, 'export'])
        ->name('focalpoints.export');
    Route::get('focalpoints/{id}/assignments', [ReportingFocalPointController::class, 'getAssignments'])
        ->name('focalpoints.assignments');

    // Target Actions
    Route::get('target-actions/by-action/{actionId}', [ReportingTargetActionController::class, 'getByAction'])
        ->name('target-actions.by-action');
    Route::post('target-actions/{id}/update-achievement', [ReportingTargetActionController::class, 'updateAchievement'])
        ->name('target-actions.update-achievement');
    Route::get('target-actions/{id}/calculate-achievement', [ReportingTargetActionController::class, 'calculateAchievement'])
        ->name('target-actions.calculate-achievement');
    Route::get('target-actions/overdue/list', [ReportingTargetActionController::class, 'getOverdueTargets'])
        ->name('target-actions.overdue');
    Route::get('target-actions/achievement-summary/{actionId}', [ReportingTargetActionController::class, 'getAchievementSummary'])
        ->name('target-actions.achievement-summary');

    // Activity Indicators
    Route::get('activity-indicators/by-activity/{activityId}', [ReportingActivityIndicatorController::class, 'getByActivity'])
        ->name('activity-indicators.by-activity');
    Route::get('activity-indicators/by-indicator/{indicatorId}', [ReportingActivityIndicatorController::class, 'getByIndicator'])
        ->name('activity-indicators.by-indicator');
    Route::post('activity-indicators/{id}/update-achievement', [ReportingActivityIndicatorController::class, 'updateAchievement'])
        ->name('activity-indicators.update-achievement');
    Route::get('activity-indicators/{id}/calculate-achievement', [ReportingActivityIndicatorController::class, 'calculateAchievement'])
        ->name('activity-indicators.calculate-achievement');
    Route::get('activity-indicators/achievement-summary/{activityId}', [ReportingActivityIndicatorController::class, 'getAchievementSummaryByActivity'])
        ->name('activity-indicators.achievement-summary');

    // Activity Focal Points
    Route::get('activity-focalpoints/by-activity/{activityId}', [ReportingActivityFocalPointController::class, 'getByActivity'])
        ->name('activity-focalpoints.by-activity');
    Route::get('activity-focalpoints/by-focalpoint/{focalpointId}', [ReportingActivityFocalPointController::class, 'getByFocalPoint'])
        ->name('activity-focalpoints.by-focalpoint');
    Route::post('activity-focalpoints/{id}/update-status', [ReportingActivityFocalPointController::class, 'updateStatus'])
        ->name('activity-focalpoints.update-status');
    Route::get('activity-focalpoints/summary/{activityId}', [ReportingActivityFocalPointController::class, 'getSummaryByActivity'])
        ->name('activity-focalpoints.summary');
    Route::get('activity-focalpoints/workload/{focalpointId}', [ReportingActivityFocalPointController::class, 'getFocalPointWorkload'])
        ->name('activity-focalpoints.workload');

    // Mappings
    Route::get('mappings/by-rp-activity/{rpActivityId}', [ReportingMappingController::class, 'getByRpActivity'])
        ->name('mappings.by-rp-activity');
    Route::get('mappings/by-external-activity/{externalActivityId}', [ReportingMappingController::class, 'getByExternalActivity'])
        ->name('mappings.by-external-activity');
    Route::get('mappings/by-external-activity/{externalActivityId}/{externalType}', [ReportingMappingController::class, 'getByExternalActivity'])
        ->name('mappings.by-external-activity-type');
    Route::post('mappings/{id}/sync', [ReportingMappingController::class, 'syncMapping'])
        ->name('mappings.sync');
    Route::post('mappings/bulk-sync', [ReportingMappingController::class, 'bulkSync'])
        ->name('mappings.bulk-sync');
    Route::get('mappings/statistics', [ReportingMappingController::class, 'getStatistics'])
        ->name('mappings.statistics');
    Route::get('mappings/{id}/validate', [ReportingMappingController::class, 'validateMapping'])
        ->name('mappings.validate');
    Route::get('mappings/needing-sync', [ReportingMappingController::class, 'getMappingsNeedingSync'])
        ->name('mappings.needing-sync');
});
