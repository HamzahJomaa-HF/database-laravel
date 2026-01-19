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
        $modules = [
            // Core Modules - using ENUM values
            'Programs' => [
                'none' => 'Cannot view or interact with any program data',
                'view' => 'Can only view program information, no modifications',
                'create' => 'Can create new programs but cannot edit or delete',
                'edit' => 'Can edit existing programs but cannot create or delete',
                'delete' => 'Can delete programs but cannot create or edit',
                'manage' => 'Full Create, Read, Update, Delete permissions',
                'full' => 'Administrator level access with all permissions'
            ],
            'Projects' => [
                'none' => 'Cannot view or interact with any project data',
                'view' => 'Can only view project information, no modifications',
                'create' => 'Can create new projects but cannot edit or delete',
                'edit' => 'Can edit existing projects but cannot create or delete',
                'delete' => 'Can delete projects but cannot create or edit',
                'manage' => 'Full Create, Read, Update, Delete permissions',
                'full' => 'Administrator level access with all permissions'
            ],
            'Users' => [
                'none' => 'Cannot view or interact with any user/employee data',
                'view' => 'Can only view user information, no modifications',
                'create' => 'Can create new users but cannot edit or delete',
                'edit' => 'Can edit existing users but cannot create or delete',
                'delete' => 'Can delete users but cannot create or edit',
                'manage' => 'Full Create, Read, Update, Delete permissions',
                'full' => 'Administrator level access with all permissions'
            ],
            'Activities' => [
                'none' => 'Cannot view or interact with any activity data',
                'view' => 'Can only view activity information, no modifications',
                'create' => 'Can create new activities but cannot edit or delete',
                'edit' => 'Can edit existing activities but cannot create or delete',
                'delete' => 'Can delete activities but cannot create or edit',
                'manage' => 'Full Create, Read, Update, Delete permissions',
                'full' => 'Administrator level access with all permissions'
            ],
            // Additional Modules
            'Dashboard' => [
                'none' => 'Cannot access the dashboard',
                'view' => 'Can view dashboard data but cannot customize',
                'full' => 'Full access to dashboard with customization options'
            ],
            'Reports' => [
                'none' => 'Cannot access any reports',
                'view' => 'Can view pre-generated reports',
                'create' => 'Can generate and view reports', // Using 'create' for generate
                'full' => 'Full access to all reporting features'
            ]
        ];

        $records = [];
        $now = now();

        foreach ($modules as $module => $accessLevels) {
            foreach ($accessLevels as $accessLevel => $description) {
                $records[] = [
                    'access_id' => (string) \Illuminate\Support\Str::uuid(),
                    'module' => $module,
                    'access_level' => $accessLevel,
                    'description' => $description,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null
                ];
            }
        }

        // Optional: Clear existing data first to avoid duplicates
        DB::table('module_access')->truncate();
        
        // Insert data into the module_access table
        DB::table('module_access')->insert($records);

        $this->command->info('Module access permissions seeded successfully!');
        $this->command->info('Total records inserted: ' . count($records));
    }
}