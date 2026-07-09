<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Activity;
use App\Models\ActivityFinancial;
use App\Models\ActivityUser;
use App\Models\Employee;
use App\Models\Program;
use App\Models\Project;
use App\Models\Portfolio;
use App\Models\Cop;
use App\Models\ActionPlan;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $employee = Auth::guard('employee')->user();
        $employee->load('role.moduleAccesses', 'credentials');

        $hasFullAccess = $employee->hasFullAccess();

        $can = [
            'users'          => $hasFullAccess || $employee->hasPermission('Users'),
            'usersCreate'    => $hasFullAccess || $employee->hasPermission('Users', 'create') || $employee->hasPermission('Users', 'manage') || $employee->hasPermission('Users', 'full'),
            'usersManage'    => $hasFullAccess || $employee->hasPermission('Users', 'manage') || $employee->hasPermission('Users', 'full'),

            'activities'     => $hasFullAccess || $employee->hasPermission('Activities'),
            'activitiesCreate' => $hasFullAccess || $employee->hasPermission('Activities', 'create') || $employee->hasPermission('Activities', 'manage') || $employee->hasPermission('Activities', 'full'),
            'activitiesManage' => $hasFullAccess || $employee->hasPermission('Activities', 'manage') || $employee->hasPermission('Activities', 'full'),

            'programs'       => $hasFullAccess || $employee->hasPermission('Programs'),
            'programsCreate' => $hasFullAccess || $employee->hasPermission('programs', 'create') || $employee->hasPermission('programs', 'manage') || $employee->hasPermission('programs', 'full'),

            'projects'       => $hasFullAccess || $employee->hasPermission('Projects'),
            'projectsCreate' => $hasFullAccess || $employee->hasPermission('projects', 'create') || $employee->hasPermission('projects', 'manage') || $employee->hasPermission('projects', 'full'),

            'portfolios'     => $hasFullAccess || $employee->hasPermission('Portfolios'),
            'portfoliosCreate' => $hasFullAccess || $employee->hasPermission('Portfolios', 'create') || $employee->hasPermission('Portfolios', 'manage') || $employee->hasPermission('Portfolios', 'full'),

            'cops'           => $hasFullAccess || $employee->hasPermission('COPs'),
            'copsCreate'     => $hasFullAccess || $employee->hasPermission('COPs', 'create') || $employee->hasPermission('COPs', 'manage') || $employee->hasPermission('COPs', 'full'),

            'employees'      => $hasFullAccess || $employee->hasPermission('Employees'),
            'employeesCreate' => $hasFullAccess || $employee->hasPermission('Employees', 'create') || $employee->hasPermission('Employees', 'manage') || $employee->hasPermission('Employees', 'full'),

            'roles'          => $hasFullAccess || $employee->hasPermission('Roles'),

            'activityUsers'  => $hasFullAccess || $employee->hasPermission('ActivityUsers'),
            'activityUsersCreate' => $hasFullAccess || $employee->hasPermission('ActivityUsers', 'create') || $employee->hasPermission('ActivityUsers', 'manage') || $employee->hasPermission('ActivityUsers', 'full'),
            'activityUsersManage' => $hasFullAccess || $employee->hasPermission('ActivityUsers', 'manage') || $employee->hasPermission('ActivityUsers', 'full'),

            'financials'     => $hasFullAccess || $employee->hasPermission('Financials'),
            'financialsCreate' => $hasFullAccess || $employee->hasPermission('Financials', 'create') || $employee->hasPermission('Financials', 'manage') || $employee->hasPermission('Financials', 'full'),

            'reports'        => $hasFullAccess || $employee->hasPermission('Reports') || $employee->hasPermission('reports'),
            'reportsCreate'  => $hasFullAccess || $employee->hasPermission('Reports', 'create') || $employee->hasPermission('Reports', 'full') || $employee->hasPermission('reports', 'create') || $employee->hasPermission('reports', 'full'),

            'actionPlans'    => $hasFullAccess || $employee->hasPermission('Reports') || $employee->hasPermission('ActionPlans'),
        ];

        // Counts — only query what the user can see
        $counts = [
            'users'         => $can['users']        ? User::count()                                          : null,
            'activities'    => $can['activities']   ? Activity::count()                                      : null,
            'programs'      => $can['programs']     ? Program::count()                                       : null,
            'projects'      => $can['projects']     ? Project::count()                                       : null,
            'portfolios'    => $can['portfolios']   ? Portfolio::count()                                     : null,
            'cops'          => $can['cops']         ? Cop::count()                                           : null,
            'employees'     => $can['employees']    ? Employee::count()                                      : null,
            'roles'         => $can['roles']        ? Role::count()                                          : null,
            'activityUsers' => $can['activityUsers'] ? ActivityUser::count()                                 : null,
            'financials'    => $can['financials']   ? ActivityFinancial::count()                             : null,
            'omt'           => $can['financials']   ? ActivityFinancial::where('financial_type','omt')->count()     : null,
            'medical'       => $can['financials']   ? ActivityFinancial::where('financial_type','medical')->count() : null,
            'actionPlans'   => $can['actionPlans']  ? ActionPlan::count()                                    : null,
        ];

        return view('dashboard.index', compact('employee', 'hasFullAccess', 'can', 'counts'));
    }
}
