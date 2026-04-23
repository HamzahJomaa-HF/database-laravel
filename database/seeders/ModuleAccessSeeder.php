<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleAccessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the modules, access levels (must match ENUM values), and descriptions
        // Allowed ENUM values: 'view', 'create', 'edit', 'delete', 'manage', 'full'
        $modules = [
            // Core Modules - based on actual route permissions
            'Programs' => [
                'view' => 'Can only view program information, no modifications',
                'create' => 'Can create new programs but cannot edit or delete',
                'edit' => 'Can edit existing programs but cannot create or delete',
                'delete' => 'Can delete programs but cannot create or edit',
                'manage' => 'Can manage all program operations including centers and sub-programs',
                'full' => 'Administrator level access with all permissions'
            ],
            'Projects' => [
                'view' => 'Can only view project information, no modifications',
                'create' => 'Can create new projects but cannot edit or delete',
                'edit' => 'Can edit existing projects but cannot create or delete',
                'delete' => 'Can delete projects but cannot create or edit',
                'manage' => 'Can manage all project operations including activities and reports',
                'full' => 'Administrator level access with all permissions'
            ],
            'Users' => [
                'view' => 'Can only view user information, no modifications. Includes export capability.',
                'create' => 'Can create new users but cannot edit or delete',
                'edit' => 'Can edit existing users but cannot create or delete',
                'delete' => 'Can delete users but cannot create or edit',
                'manage' => 'Can manage all user operations including bulk operations, import, and export',
                'full' => 'Administrator level access with all permissions'
            ],
            'Activities' => [
                'view' => 'Can only view activity information, no modifications. Includes export capability.',
                'create' => 'Can create new activities but cannot edit or delete',
                'edit' => 'Can edit existing activities but cannot create or delete',
                'delete' => 'Can delete activities but cannot create or edit',
                'manage' => 'Can manage all activity operations including children activities, import, and export',
                'full' => 'Administrator level access with all permissions'
            ],
            'Employees' => [
                'view' => 'Can only view employee information, no modifications. Includes export capability.',
                'create' => 'Can create new employees but cannot edit or delete',
                'edit' => 'Can edit existing employees but cannot create or delete',
                'delete' => 'Can delete employees but cannot create or edit',
                'manage' => 'Can manage all employee operations including activation/deactivation, import, and export',
                'full' => 'Administrator level access with all permissions'
            ],
            'Roles' => [
                'view' => 'Can only view roles and permissions',
                'create' => 'Can create new roles but cannot edit or delete',
                'edit' => 'Can edit existing roles but cannot create or delete',
                'delete' => 'Can delete roles but cannot create or edit',
                'manage' => 'Can manage role permissions and assignments',
                'full' => 'Administrator level access with all permissions',
            ],
            'Portfolios' => [
                'view' => 'Can only view portfolio information, no modifications',
                'create' => 'Can create new portfolios but cannot edit or delete',
                'edit' => 'Can edit existing portfolios but cannot create or delete',
                'delete' => 'Can delete portfolios but cannot create or edit',
                'manage' => 'Can manage all portfolio operations',
                'full' => 'Administrator level access with all permissions',
            ],
            'COPs' => [
                'view' => 'Can only view Community of Practice (COP) information, no modifications',
                'create' => 'Can create new COPs but cannot edit or delete',
                'edit' => 'Can edit existing COPs but cannot create or delete',
                'delete' => 'Can delete COPs but cannot create or edit',
                'manage' => 'Can manage all COP operations',
                'full' => 'Administrator level access with all permissions',
            ],
            'ActivityUsers' => [
                'view' => 'Can only view activity-user assignments, no modifications. Includes export capability.',
                'create' => 'Can create new activity-user assignments but cannot edit or delete',
                'edit' => 'Can edit existing activity-user assignments but cannot create or delete',
                'delete' => 'Can delete activity-user assignments but cannot create or edit',
                'manage' => 'Can manage all activity-user operations including import/export, trash/restore',
                'full' => 'Administrator level access with all permissions for activity-user management',
            ],
            'module_access' => [
                'view' => 'Can view module access configurations',
                'create' => 'Can create new module access configurations',
                'edit' => 'Can edit existing module access configurations',
                'delete' => 'Can delete module access configurations',
                'manage' => 'Can manage all module access operations',
                'full' => 'Full administrator access to module access management',
            ],
            'reports' => [
                'view' => 'Can view reports',
                'create' => 'Can create and generate reports',
                'manage' => 'Can manage all reporting operations including import/export',
                'full' => 'Full access to all reporting features including imports',
            ],
            'Dashboard' => [
                'view' => 'Can view dashboard data but cannot customize',
                'full' => 'Full access to dashboard with customization options'
            ],
            'Reports' => [
                'view' => 'Can view pre-generated reports',
                'create' => 'Can generate and view reports',
                'full' => 'Full access to all reporting features'
            ],
            // Action Plans module (using Reports permissions as per routes)
            'ActionPlans' => [
                'view' => 'Can view action plans',
                'create' => 'Can create action plans',
                'full' => 'Full access to action plans including bulk delete'
            ],
        ];

        $now = now();
        $insertCount = 0;

        foreach ($modules as $module => $accessLevels) {
            foreach ($accessLevels as $accessLevel => $description) {
                
                // Skip if access_level is not in allowed ENUM values
                $allowedAccessLevels = ['view', 'create', 'edit', 'delete', 'manage', 'full'];
                if (!in_array($accessLevel, $allowedAccessLevels)) {
                    $this->command->warn("Skipping '{$accessLevel}' for module '{$module}' - not in allowed ENUM values");
                    continue;
                }

                $exists = DB::table('module_access')
                    ->where('module', $module)
                    ->where('access_level', $accessLevel)
                    ->exists();

                if (!$exists) {
                    DB::table('module_access')->insert([
                        'access_id' => (string) \Illuminate\Support\Str::uuid(),
                        'module' => $module,
                        'access_level' => $accessLevel,
                        'description' => $description,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]);
                    $insertCount++;
                }
            }
        }

        $this->command->info('Module access permissions seeded successfully!');
        $this->command->info('Total records inserted: ' . $insertCount);
        
        // Display summary of modules seeded
        $this->command->info("\n=== MODULES SEEDED ===");
        foreach (array_keys($modules) as $module) {
            $count = count($modules[$module]);
            $this->command->line("- {$module} ({$count} access levels)");
        }
    }
}