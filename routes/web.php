<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\ActivityController;


Route::get('/', function () {
    return view('welcome');
});



Route::get('/activity/{external_activity_id}/link', [ActivityController::class, 'index'])->name('link.activities'); // Example route for ActivityController

// Use {user_id} to match your controller methods
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::get('/users/{user_id}/edit', [UserController::class, 'edit'])->name('users.edit'); // Changed to user_id
Route::put('/users/{user_id}', [UserController::class, 'update'])->name('users.update'); // Changed to user_id
Route::delete('/users/{user_id}', [UserController::class, 'destroy'])->name('users.destroy'); // Changed to user_id
Route::get('/users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');

// WEB Analytics routes (return views)
Route::get('/users/dashboard', [UserController::class, 'dashboard'])->name('users.dashboard');
Route::get('/users/statistics', [UserController::class, 'statistics'])->name('users.statistics');
Route::get('/users/reports', [UserController::class, 'reports'])->name('users.reports');
// Auth routes
Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// Export route
Route::get('/users/export/excel', [UserController::class, 'exportExcel'])->name('users.export.excel');

// import functionality
Route::get('/users/import', [UserController::class, 'showImportForm'])->name('users.import.form');
Route::post('/users/import', [UserController::class, 'import'])->name('users.import');
Route::get('/users/import/template', [UserController::class, 'downloadTemplate'])->name('users.import.template');

// Bulk actions
Route::delete('/users/bulk-delete', [UserController::class, 'bulkDelete'])->name('users.bulk-delete');
Route::delete('/users/delete-all', [UserController::class, 'deleteAll'])->name('users.delete-all');




use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// Public routes - NO middleware needed
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/resend-otp', [LoginController::class, 'resendOtp'])->name('resend.otp');

Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');

// Protected routes - Require authentication
Route::middleware(['auth.system'])->group(function () {
    Route::get('/dashboard', function () {
        // Simple dashboard view
        $user = auth()->guard('system')->user();
        return view('dashboard', ['user' => $user]);
    })->name('dashboard');
    
    // Main Resources
    Route::resource('components', ReportingComponentController::class);
    Route::resource('programs', ReportingProgramController::class);
    Route::resource('units', ReportingUnitController::class);
    Route::resource('actions', ReportingActionController::class);
    Route::resource('activities', ReportingActivityController::class);
    Route::resource('focalpoints', ReportingFocalPointController::class);
    Route::resource('target-actions', ReportingTargetActionController::class);
    
    // Pivot/Join Tables
    Route::resource('activity-indicators', ReportingActivityIndicatorController::class);
    Route::resource('activity-focalpoints', ReportingActivityFocalPointController::class);
    Route::resource('mappings', ReportingMappingController::class);
    
    // ========== Additional Routes for AJAX/API ==========
    
    // Components
    // (No additional routes needed beyond basic resource)
    
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
    
    // Indicators
    
    
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

    // ========== IMPORT ROUTES ==========
   Route::prefix('import')->name('import.')->group(function () {
    Route::match(['GET', 'POST'], '/', [ReportingImportController::class, 'handleImport'])
        ->name('handle');
    Route::get('/template', [ReportingImportController::class, 'downloadTemplate'])
        ->name('download-template');






        
});
}); // Close the main reporting group
