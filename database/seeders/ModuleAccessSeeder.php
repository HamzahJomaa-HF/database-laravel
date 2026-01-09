<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\ModuleAccess;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ModuleAccessSeeder extends Seeder
{
    public function run()
    {
        // Get employees
        $superAdmin = Employee::where('email', 'admin@hariri.org')->first();
        $hrManager = Employee::where('email', 'hr@hariri.org')->first();
        $programManager = Employee::where('email', 'programs@hariri.org')->first();
        $projectCoordinator = Employee::where('email', 'projects@hariri.org')->first();
        $fieldOfficer = Employee::where('email', 'field@hariri.org')->first();
        $viewer = Employee::where('email', 'viewer@hariri.org')->first();
        
        // Clear existing module access
        ModuleAccess::truncate();
        
        // 1. Super Admin: Full access to everything
        ModuleAccess::create([
            'access_id' => Str::uuid(),
            'employee_id' => $superAdmin->employee_id,
            'module' => 'all',
            'access_level' => 'full',
        ]);
        
        // 2. HR Manager: Full access to system modules
        $hrModules = [
            ['module' => 'users', 'access_level' => 'manage'],
            ['module' => 'reports', 'access_level' => 'manage'],
            ['module' => 'action_plans', 'access_level' => 'view'],
            ['module' => 'surveys', 'access_level' => 'view'],
        ];
        
        foreach ($hrModules as $module) {
            ModuleAccess::create([
                'access_id' => Str::uuid(),
                'employee_id' => $hrManager->employee_id,
                'module' => $module['module'],
                'access_level' => $module['access_level'],
            ]);
        }
        
        // 3. Program Manager: Manage programs, projects, activities
        $programManagerModules = [
            ['module' => 'programs', 'access_level' => 'manage'],
            ['module' => 'projects', 'access_level' => 'manage'],
            ['module' => 'activities', 'access_level' => 'manage'],
            ['module' => 'surveys', 'access_level' => 'manage'],
            ['module' => 'reports', 'access_level' => 'manage'],
        ];
        
        foreach ($programManagerModules as $module) {
            ModuleAccess::create([
                'access_id' => Str::uuid(),
                'employee_id' => $programManager->employee_id,
                'module' => $module['module'],
                'access_level' => $module['access_level'],
            ]);
        }
        
        // 4. Project Coordinator: Edit projects and activities
        $coordinatorModules = [
            ['module' => 'projects', 'access_level' => 'edit'],
            ['module' => 'activities', 'access_level' => 'edit'],
            ['module' => 'surveys', 'access_level' => 'edit'],
        ];
        
        foreach ($coordinatorModules as $module) {
            ModuleAccess::create([
                'access_id' => Str::uuid(),
                'employee_id' => $projectCoordinator->employee_id,
                'module' => $module['module'],
                'access_level' => $module['access_level'],
            ]);
        }
        
        // 5. Field Officer: Create activities and surveys
        $fieldOfficerModules = [
            ['module' => 'activities', 'access_level' => 'create'],
            ['module' => 'surveys', 'access_level' => 'create'],
        ];
        
        foreach ($fieldOfficerModules as $module) {
            ModuleAccess::create([
                'access_id' => Str::uuid(),
                'employee_id' => $fieldOfficer->employee_id,
                'module' => $module['module'],
                'access_level' => $module['access_level'],
            ]);
        }
        
        // 6. Viewer: View access only
        $viewerModules = [
            ['module' => 'activities', 'access_level' => 'view'],
            ['module' => 'projects', 'access_level' => 'view'],
            ['module' => 'programs', 'access_level' => 'view'],
            ['module' => 'reports', 'access_level' => 'view'],
        ];
        
        foreach ($viewerModules as $module) {
            ModuleAccess::create([
                'access_id' => Str::uuid(),
                'employee_id' => $viewer->employee_id,
                'module' => $module['module'],
                'access_level' => $module['access_level'],
            ]);
        }
        
        $this->command->info('✓ Module access seeded successfully!');
    }
}