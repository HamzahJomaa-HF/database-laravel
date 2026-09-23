<?php

/**
 * Single source of truth for every permission checkbox shown on the
 * Roles > Edit/Create Permissions tab. Each [module => [access_level => description]]
 * entry becomes one row in the module_access table (via ModuleAccessSeeder) and one
 * checkbox in resources/views/roles/edit.blade.php + create.blade.php.
 *
 * Every distinct action/button in the app should have exactly one key here.
 * "manage" and "full" are kept on every module as a blanket override: a role that
 * holds a module's "manage" or "full" checkbox is granted every other action in
 * that module automatically (see App\Support\Permissions::check()).
 */

return [

    'Programs' => [
        'view' => 'View programs, centers and sub-programs',
        'create' => 'Add centers, programs and sub-programs',
        'edit' => 'Edit centers, programs and sub-programs',
        'delete' => 'Delete programs',
        'manage' => 'Manage all program operations',
        'full' => 'Full administrator access to Programs',
    ],

    'Projects' => [
        'view' => 'View projects',
        'create' => 'Add new projects',
        'edit' => 'Edit existing projects',
        'delete' => 'Delete projects',
        'manage' => 'Manage all project operations',
        'full' => 'Full administrator access to Projects',
    ],

    'Users' => [
        'view' => 'View users',
        'create' => 'Add new users',
        'edit' => 'Edit existing users',
        'delete' => 'Delete users',
        'export' => 'Export users to Excel',
        'import' => 'Import users from a file',
        'download_template' => 'Download the users import template',
        'bulk_delete' => 'Bulk delete selected users',
        'manage' => 'Manage all user operations',
        'full' => 'Full administrator access to Users',
    ],

    'Employees' => [
        'view' => 'View employees',
        'create' => 'Add new employees',
        'edit' => 'Edit existing employees',
        'delete' => 'Delete employees',
        'export' => 'Export employees to Excel',
        'import' => 'Import employees from a file',
        'activate' => 'Activate employee accounts',
        'deactivate' => 'Deactivate employee accounts',
        'toggle_status' => 'Toggle an employee\'s active status',
        'restore' => 'Restore a deleted employee',
        'force_delete' => 'Permanently delete an employee',
        'view_trashed' => 'View trashed (deleted) employees',
        'manage' => 'Manage all employee operations',
        'full' => 'Full administrator access to Employees',
    ],

    'Roles' => [
        'view' => 'View roles and permissions',
        'create' => 'Add new roles',
        'edit' => 'Edit roles and their permissions',
        'delete' => 'Delete roles',
        'manage' => 'Manage all role operations',
        'full' => 'Full administrator access to Roles',
    ],

    'module_access' => [
        'view' => 'View module access configurations',
        'create' => 'Add module access configurations',
        'edit' => 'Edit module access configurations',
        'delete' => 'Delete module access configurations',
        'manage' => 'Manage all module access operations',
        'full' => 'Full administrator access to Module Access',
    ],

    'Portfolios' => [
        'view' => 'View portfolios',
        'create' => 'Add new portfolios',
        'edit' => 'Edit existing portfolios',
        'delete' => 'Delete portfolios',
        'manage' => 'Manage all portfolio operations',
        'full' => 'Full administrator access to Portfolios',
    ],

    'COPs' => [
        'view' => 'View Communities of Practice (COPs)',
        'create' => 'Add new COPs',
        'edit' => 'Edit existing COPs',
        'delete' => 'Delete COPs',
        'manage' => 'Manage all COP operations',
        'full' => 'Full administrator access to COPs',
    ],

    'Activities' => [
        'view' => 'View all activities across the organization',
        'view_own' => 'View only activities where the employee is assigned as a focal point',
        'view_all' => 'View every activity read-only, even if also scoped as a focal point to specific ones — does not grant edit/delete outside assigned activities',
        'create' => 'Add new activities',
        'edit' => 'Edit existing activities',
        'delete' => 'Delete activities',
        'export' => 'Export activities to Excel',
        'import' => 'Import activities from a file',
        'download_template' => 'Download the activities import template',
        'bulk_delete' => 'Bulk delete selected activities',
        'create_child' => 'Add child activities',
        'manage' => 'Manage all activity operations',
        'full' => 'Full administrator access to Activities',
    ],

    'ActivityUsers' => [
        'view' => 'View activity-user assignments',
        'create' => 'Add new activity-user assignments',
        'edit' => 'Edit existing activity-user assignments',
        'delete' => 'Delete activity-user assignments',
        'export' => 'Export activity-user assignments to Excel',
        'import' => 'Import activity-user assignments from a file',
        'download_template' => 'Download the activity-users import template',
        'bulk_delete' => 'Bulk delete selected activity-user assignments',
        'restore' => 'Restore a deleted activity-user assignment',
        'force_delete' => 'Permanently delete an activity-user assignment',
        'view_trash' => 'View trashed activity-user assignments',
        'manage' => 'Manage all activity-user operations',
        'full' => 'Full administrator access to Activity Users',
    ],

    'Financials' => [
        'view' => 'View financial records (OMT, medical)',
        'create' => 'Add new financial records',
        'edit' => 'Edit existing financial records',
        'delete' => 'Delete financial records',
        'import' => 'Import financial records from a file',
        'download_template' => 'Download the financials import template',
        'bulk_delete' => 'Bulk delete selected financial records',
        'visualization' => 'View financial visualization/charts',
        'manage' => 'Manage all financial operations',
        'full' => 'Full administrator access to Financials',
    ],

    'reports' => [
        'view' => 'View the reporting import tool',
        'create' => 'Process a reporting data import',
        'preview' => 'Preview reporting import data before processing',
        'download_template' => 'Download the reporting import template',
        'manage' => 'Manage all reporting import operations',
        'full' => 'Full administrator access to Reporting',
    ],

    'Reports' => [
        'view' => 'View pre-generated reports',
        'create' => 'Generate reports',
        'full' => 'Full access to all reporting features',
    ],

    'ActionPlans' => [
        'view' => 'View action plans',
        'download' => 'Download an action plan',
        'delete' => 'Delete an action plan',
        'bulk_delete' => 'Bulk delete selected action plans',
        'manage' => 'Manage all action plan operations',
        'full' => 'Full access to action plans including bulk delete',
    ],

    'Dashboard' => [
        'view' => 'View dashboard data',
        'full' => 'Full access to the dashboard with customization options',
    ],

    'Analytics' => [
        'view' => 'View the analytics dashboard',
        'full' => 'Full administrator access to Analytics',
    ],

];
