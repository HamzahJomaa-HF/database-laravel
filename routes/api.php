<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ActivityController;
use App\Http\Controllers\API\ProgramController;
use App\Http\Controllers\API\ProjectController;
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
use App\Http\Controllers\API\PortfolioActivityController;
use App\Http\Controllers\API\PortfolioController;
use App\Http\Controllers\API\DiplomaController;
use App\Http\Controllers\API\UserDiplomaController;
use App\Http\Controllers\API\NationalityController;
use App\Http\Controllers\API\UserNationalityController;
use App\Http\Controllers\API\ProjectPortfolioController;

        






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

// Project 


Route::get('/projects', [ProjectController::class, 'index']);
Route::post('/projects', [ProjectController::class, 'store']);
Route::put('/projects/{id}', [ProjectController::class, 'update']);
Route::delete('/projects/{id}', [ProjectController::class, 'destroy']);


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

// Portfolios
Route::prefix('portfolios')->group(function () {
    Route::get('/', [PortfolioController::class, 'index']);          // Get all portfolios
    Route::post('/', [PortfolioController::class, 'store']);         // Create a new portfolio
    Route::put('/{id}', [PortfolioController::class, 'update']);     // Update a portfolio
    Route::delete('/{id}', [PortfolioController::class, 'destroy']); // Delete a portfolio
});

// Portfolio Activities (many-to-many pivot)
Route::prefix('portfolio-activities')->group(function () {
    Route::get('/', [PortfolioActivityController::class, 'index']);           // Get all portfolio-activity links
    Route::post('/', [PortfolioActivityController::class, 'store']);          // Add a new activity to a portfolio
    Route::put('/{id}', [PortfolioActivityController::class, 'update']);      // Update a portfolio-activity link (if needed)
    Route::delete('/{id}', [PortfolioActivityController::class, 'destroy']);  // Remove a portfolio-activity link
});



// All API routes for Users
Route::prefix('users')->group(function () {
    // RESTful routes: index, store, update, destroy
    Route::get('/', [UserController::class, 'index']);      // GET /api/users or /api/users?id=UUID
    Route::post('/', [UserController::class, 'store']);     // POST /api/users
    Route::put('{id}', [UserController::class, 'update']);  // PUT /api/users/{id}
    Route::delete('{id}', [UserController::class, 'destroy']); // DELETE /api/users/{id}
});


// Diplomas
Route::prefix('diplomas')->group(function () {
    Route::get('/', [DiplomaController::class, 'index']);          
    Route::post('/', [DiplomaController::class, 'store']);        
    Route::put('/{id}', [DiplomaController::class, 'update']);    
    Route::delete('/{id}', [DiplomaController::class, 'destroy']);
});

// User Diplomas (pivot table linking users and diplomas)
Route::prefix('user-diplomas')->group(function () {
    Route::get('/', [UserDiplomaController::class, 'index']);          
    Route::post('/', [UserDiplomaController::class, 'store']);         
    Route::put('/{id}', [UserDiplomaController::class, 'update']);     
    Route::delete('/{id}', [UserDiplomaController::class, 'destroy']); 
   
});
// Nationality
Route::get('/nationalities', [NationalityController::class, 'index']);
Route::get('/nationalities/{nationality_id}', [NationalityController::class, 'show']);
Route::post('/nationalities', [NationalityController::class, 'store']);
Route::put('/nationalities/{nationality_id}', [NationalityController::class, 'update']);
Route::delete('/nationalities/{nationality_id}', [NationalityController::class, 'destroy']);

//UserNationality
Route::get('/user-nationalities', [UserNationalityController::class, 'index']);
Route::get('/user-nationalities/{users_nationality_id}', [UserNationalityController::class, 'show']);
Route::post('/user-nationalities', [UserNationalityController::class, 'store']);
Route::put('/user-nationalities/{users_nationality_id}', [UserNationalityController::class, 'update']);
Route::delete('/user-nationalities/{users_nationality_id}', [UserNationalityController::class, 'destroy']);


Route::prefix('activity-users')->group(function () {
    // List all ActivityUser records
    Route::get('/', [ActivityUserController::class, 'index']);

    // Create a new ActivityUser
    Route::post('/', [ActivityUserController::class, 'store']);

    // Show a single ActivityUser
    Route::get('/{id}', [ActivityUserController::class, 'show']);

    // Update an existing ActivityUser
    Route::put('/{id}', [ActivityUserController::class, 'update']);

    // Delete an ActivityUser
    Route::delete('/{id}', [ActivityUserController::class, 'destroy']);

    // Upload a file without an ID
    Route::post('/upload', [ActivityUserController::class, 'uploadFileWithoutId']);
    Route::post('/activity-users/debug-csv', [ActivityUserController::class, 'debugCsv']);
});

// routes/api.php
Route::apiResource('project-portfolios', ProjectPortfolioController::class);
Route::get('portfolios/{portfolioId}/projects', [ProjectPortfolioController::class, 'getProjectsByPortfolio']);
Route::get('projects/{projectId}/portfolios', [ProjectPortfolioController::class, 'getPortfoliosByProject']);