<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Get all roles
        $superAdmin = Role::where('role_name', 'Super Admin')->first();
        $hrManager = Role::where('role_name', 'HR Manager')->first();
        $programManager = Role::where('role_name', 'Program Manager')->first();
        $projectCoordinator = Role::where('role_name', 'Project Coordinator')->first();
        $fieldOfficer = Role::where('role_name', 'Field Officer')->first();
        $viewer = Role::where('role_name', 'Viewer')->first();
        
        // Get all permissions
        $allPermissions = Permission::all();
        $viewPermissions = Permission::whereIn('group', ['activities', 'projects', 'programs', 'reports'])
                                    ->where('name', 'like', 'view-%')
                                    ->get();
        
        // Clear existing role permissions first
        \DB::table('role_permissions')->truncate();
        
        // 1. Super Admin: ALL permissions
        $this->attachPermissionsWithIds($superAdmin, $allPermissions);
        
        // 2. HR Manager: System + Users + View permissions
        $hrPermissions = Permission::whereIn('group', ['system', 'users', 'reports'])
                                  ->orWhere('name', 'like', 'view-%')
                                  ->get();
        $this->attachPermissionsWithIds($hrManager, $hrPermissions);
        
        // 3. Program Manager: Programs, Projects, Activities, Reports
        $programManagerPermissions = Permission::whereIn('group', [
            'programs', 'projects', 'activities', 'reports', 'surveys'
        ])->get();
        $this->attachPermissionsWithIds($programManager, $programManagerPermissions);
        
        // 4. Project Coordinator: Projects, Activities (no delete)
        $coordinatorPermissions = Permission::whereIn('group', ['projects', 'activities', 'surveys'])
                                          ->where('name', 'not like', 'delete-%')
                                          ->where('name', 'not like', 'manage-%')
                                          ->get();
        $this->attachPermissionsWithIds($projectCoordinator, $coordinatorPermissions);
        
        // 5. Field Officer: Create/view activities, surveys
        $fieldOfficerPermissions = Permission::whereIn('name', [
            'view-activities', 'create-activities', 'edit-activities',
            'view-surveys', 'create-surveys', 'edit-surveys',
        ])->get();
        $this->attachPermissionsWithIds($fieldOfficer, $fieldOfficerPermissions);
        
        // 6. Viewer: Only view permissions
        $this->attachPermissionsWithIds($viewer, $viewPermissions);
        
        $this->command->info('✓ Role permissions assigned successfully!');
    }
    
    /**
     * Attach permissions with UUIDs for role_permission_id column
     */
    private function attachPermissionsWithIds($role, $permissions)
    {
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'role_permission_id' => Str::uuid(),
                'role_id' => $role->role_id,
                'permission_id' => $permission->permission_id,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        \DB::table('role_permissions')->insert($data);
    }
}