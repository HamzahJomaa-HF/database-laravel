<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PermissionSeeder extends Seeder
{
    private $permissionGroups = [
        // Module Access Permissions
        'module_access' => [
            ['name' => 'access-activities', 'group' => 'modules', 'description' => 'Access to Activities module'],
            ['name' => 'access-users', 'group' => 'modules', 'description' => 'Access to Users module'],
            ['name' => 'access-projects', 'group' => 'modules', 'description' => 'Access to Projects module'],
            ['name' => 'access-programs', 'group' => 'modules', 'description' => 'Access to Programs module'],
            ['name' => 'access-action_plans', 'group' => 'modules', 'description' => 'Access to Action Plans module'],
            ['name' => 'access-surveys', 'group' => 'modules', 'description' => 'Access to Surveys module'],
            ['name' => 'access-reports', 'group' => 'modules', 'description' => 'Access to Reports module'],
        ],
        
        // Activities Permissions
        'activities' => [
            ['name' => 'view-activities', 'group' => 'activities', 'description' => 'View activities'],
            ['name' => 'create-activities', 'group' => 'activities', 'description' => 'Create new activities'],
            ['name' => 'edit-activities', 'group' => 'activities', 'description' => 'Edit activities'],
            ['name' => 'delete-activities', 'group' => 'activities', 'description' => 'Delete activities'],
            ['name' => 'manage-activities', 'group' => 'activities', 'description' => 'Full management of activities'],
        ],
        
        // Users Permissions (external users table)
        'users' => [
            ['name' => 'view-users', 'group' => 'users', 'description' => 'View external users'],
            ['name' => 'create-users', 'group' => 'users', 'description' => 'Create new users'],
            ['name' => 'edit-users', 'group' => 'users', 'description' => 'Edit users'],
            ['name' => 'delete-users', 'group' => 'users', 'description' => 'Delete users'],
            ['name' => 'export-users', 'group' => 'users', 'description' => 'Export users data'],
        ],
        
        // Projects Permissions
        'projects' => [
            ['name' => 'view-projects', 'group' => 'projects', 'description' => 'View projects'],
            ['name' => 'create-projects', 'group' => 'projects', 'description' => 'Create new projects'],
            ['name' => 'edit-projects', 'group' => 'projects', 'description' => 'Edit projects'],
            ['name' => 'delete-projects', 'group' => 'projects', 'description' => 'Delete projects'],
        ],
        
        // Programs Permissions
        'programs' => [
            ['name' => 'view-programs', 'group' => 'programs', 'description' => 'View programs'],
            ['name' => 'create-programs', 'group' => 'programs', 'description' => 'Create new programs'],
            ['name' => 'edit-programs', 'group' => 'programs', 'description' => 'Edit programs'],
            ['name' => 'delete-programs', 'group' => 'programs', 'description' => 'Delete programs'],
        ],
        
        // Action Plans Permissions
        'action_plans' => [
            ['name' => 'view-action_plans', 'group' => 'action_plans', 'description' => 'View action plans'],
            ['name' => 'create-action_plans', 'group' => 'action_plans', 'description' => 'Create action plans'],
            ['name' => 'edit-action_plans', 'group' => 'action_plans', 'description' => 'Edit action plans'],
            ['name' => 'upload-action_plans', 'group' => 'action_plans', 'description' => 'Upload Excel action plans'],
        ],
        
        // Surveys Permissions
        'surveys' => [
            ['name' => 'view-surveys', 'group' => 'surveys', 'description' => 'View surveys'],
            ['name' => 'create-surveys', 'group' => 'surveys', 'description' => 'Create surveys'],
            ['name' => 'edit-surveys', 'group' => 'surveys', 'description' => 'Edit surveys'],
            ['name' => 'analyze-surveys', 'group' => 'surveys', 'description' => 'Analyze survey results'],
        ],
        
        // Reports Permissions
        'reports' => [
            ['name' => 'view-reports', 'group' => 'reports', 'description' => 'View reports'],
            ['name' => 'generate-reports', 'group' => 'reports', 'description' => 'Generate reports'],
            ['name' => 'export-reports', 'group' => 'reports', 'description' => 'Export reports'],
        ],
        
        // System Permissions
        'system' => [
            ['name' => 'manage-employees', 'group' => 'system', 'description' => 'Manage employees'],
            ['name' => 'manage-roles', 'group' => 'system', 'description' => 'Manage roles'],
            ['name' => 'manage-permissions', 'group' => 'system', 'description' => 'Manage permissions'],
            ['name' => 'manage-module-access', 'group' => 'system', 'description' => 'Manage module access'],
            ['name' => 'system-settings', 'group' => 'system', 'description' => 'Access system settings'],
        ],
    ];

    public function run()
    {
        foreach ($this->permissionGroups as $group => $permissions) {
            foreach ($permissions as $permission) {
                Permission::create([
                    'permission_id' => Str::uuid(),
                    'name' => $permission['name'],
                    'group' => $permission['group'],
                    'description' => $permission['description'],
                ]);
            }
        }
        
        $this->command->info('✓ Permissions seeded successfully!');
        $this->command->info('  Total permissions: ' . count($this->getAllPermissions()));
    }
    
    private function getAllPermissions()
    {
        $all = [];
        foreach ($this->permissionGroups as $group => $permissions) {
            $all = array_merge($all, $permissions);
        }
        return $all;
    }
}