<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\ProjectCenterController;
use App\Http\Controllers\API\SurveyController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\CopController;
use App\Http\Controllers\API\ActivityUserController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ProjectEmployeeController;
use App\Http\Controllers\API\ProjectActivityController;
use App\Http\Controllers\API\QuestionsController;
use App\Http\Controllers\API\SurveyQuestionsController;
use App\Http\Controllers\API\ResponsesController;
use App\Http\Controllers\API\AnswersController;






// Users
Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// Activities
Route::get('/activities', [ActivityController::class, 'index']);
Route::post('/activities', [ActivityController::class, 'store']);
Route::get('/activities/{id}', [ActivityController::class, 'show']);
Route::put('/activities/{id}', [ActivityController::class, 'update']);
Route::delete('/activities/{id}', [ActivityController::class, 'destroy']);

// Programs with prefix
Route::prefix('programs')->group(function () {
    Route::get('/', [ProgramController::class, 'index']);
    Route::post('/', [ProgramController::class, 'store']);
    Route::put('/{id}', [ProgramController::class, 'update']);
    Route::delete('/{id}', [ProgramController::class, 'destroy']);
});

// Project Centers
Route::get('/project-centers', [ProjectCenterController::class, 'index']);
Route::post('/project-centers', [ProjectCenterController::class, 'store']);
Route::put('/project-centers/{id}', [ProjectCenterController::class, 'update']);
Route::delete('/project-centers/{id}', [ProjectCenterController::class, 'destroy']);

// Surveys
Route::get('/surveys', [SurveyController::class, 'index']);
Route::post('/surveys', [SurveyController::class, 'store']);
Route::put('/surveys/{id}', [SurveyController::class, 'update']);
Route::delete('/surveys/{id}', [SurveyController::class, 'destroy']);

// Roles
Route::prefix('roles')->group(function () {
    Route::get('/', [RoleController::class, 'index']);         
    Route::post('/', [RoleController::class, 'store']);        
    Route::put('/{id}', [RoleController::class, 'update']);    
    Route::delete('/{id}', [RoleController::class, 'destroy']); 
});

// COPs
Route::prefix('cops')->group(function () {
    Route::get('/', [CopController::class, 'index']);
    Route::post('/', [CopController::class, 'store']);
    Route::put('/{id}', [CopController::class, 'update']);
    Route::delete('/{id}', [CopController::class, 'destroy']);
});


// Activity Users Routes
Route::get('/activity-users', [ActivityUserController::class, 'index']);
Route::get('/activity-users/{id}', [ActivityUserController::class, 'show']);
Route::post('/activity-users', [ActivityUserController::class, 'store']);
Route::put('/activity-users/{id}', [ActivityUserController::class, 'update']);
Route::delete('/activity-users/{id}', [ActivityUserController::class, 'destroy']);
//Route::get('/activities/{activity_id}/users', [ActivityUserController::class, 'usersByActivity']);
//Route::get('/users/{user_id}/activities', [ActivityUserController::class, 'activitiesByUser']);


// Employees
Route::prefix('employees')->group(function () {
    // Get all employees or a specific employee by ID
    Route::get('/', [EmployeeController::class, 'index']);
    Route::post('/', [EmployeeController::class, 'store']);
    Route::put('/{id}', [EmployeeController::class, 'update']);
    Route::delete('/{id}', [EmployeeController::class, 'destroy']);
});
// Project Employees
Route::prefix('project-employees')->group(function () {
    Route::get('/', [ProjectEmployeeController::class, 'index']);          
   // Route::get('/{id}', [ProjectEmployeeController::class, 'show']);       
    Route::post('/', [ProjectEmployeeController::class, 'store']);         
    Route::put('/{id}', [ProjectEmployeeController::class, 'update']);     
    Route::delete('/{id}', [ProjectEmployeeController::class, 'destroy']); 
});
// Project Activities
Route::prefix('project-activities')->group(function () {
    Route::get('/', [ProjectActivityController::class, 'index']);
    Route::post('/', [ProjectActivityController::class, 'store']);
    Route::put('/{id}', [ProjectActivityController::class, 'update']);
    Route::delete('/{id}', [ProjectActivityController::class, 'destroy']);
});
// Questions
Route::prefix('questions')->group(function () {
    Route::get('/', [QuestionsController::class, 'index']);
    Route::post('/', [QuestionsController::class, 'store']);
    Route::put('/{id}', [QuestionsController::class, 'update']);
    Route::delete('/{id}', [QuestionsController::class, 'destroy']);
});
// Survey Questions
Route::prefix('survey-questions')->group(function () {
    Route::get('/', [SurveyQuestionsController::class, 'index']);
    Route::post('/', [SurveyQuestionsController::class, 'store']);
    Route::put('/{id}', [SurveyQuestionsController::class, 'update']);
    Route::delete('/{id}', [SurveyQuestionsController::class, 'destroy']);
});
// Responses
Route::prefix('responses')->group(function () {
    Route::get('/', [ResponsesController::class, 'index']);
    Route::post('/', [ResponsesController::class, 'store']);
    Route::put('/{id}', [ResponsesController::class, 'update']);
    Route::delete('/{id}', [ResponsesController::class, 'destroy']);
});
// Answers
Route::prefix('answers')->group(function () {
    Route::get('/', [AnswersController::class, 'index']);
    Route::post('/', [AnswersController::class, 'store']);
    Route::put('/{id}', [AnswersController::class, 'update']);
    Route::delete('/{id}', [AnswersController::class, 'destroy']);
});





