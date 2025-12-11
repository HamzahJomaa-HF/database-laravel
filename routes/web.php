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
Route::get('/', function () {
    return view('welcome');
});
