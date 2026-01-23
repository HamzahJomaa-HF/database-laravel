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
                'view' => 'Can only view program information, no modifications',
                'create' => 'Can create new programs but cannot edit or delete',
                'edit' => 'Can edit existing programs but cannot create or delete',
                'delete' => 'Can delete programs but cannot create or edit',
                'full' => 'Administrator level access with all permissions'
            ],
            'Projects' => [
                'view' => 'Can only view project information, no modifications',
                'create' => 'Can create new projects but cannot edit or delete',
                'edit' => 'Can edit existing projects but cannot create or delete',
                'delete' => 'Can delete projects but cannot create or edit',
                'full' => 'Administrator level access with all permissions'
            ],
            'Users' => [
                'view' => 'Can only view user information, no modifications',
                'create' => 'Can create new users but cannot edit or delete',
                'edit' => 'Can edit existing users but cannot create or delete',
                'delete' => 'Can delete users but cannot create or edit',
                'full' => 'Administrator level access with all permissions'
            ],
            'Activities' => [
                'view' => 'Can only view activity information, no modifications',
                'create' => 'Can create new activities but cannot edit or delete',
                'edit' => 'Can edit existing activities but cannot create or delete',
                'delete' => 'Can delete activities but cannot create or edit',
                'full' => 'Administrator level access with all permissions'
            ],
            'Employees' => [
                'view'   => 'Can only view employee information, no modifications',
                'create' => 'Can create new employees but cannot edit or delete',
                'edit'   => 'Can edit existing employees but cannot create or delete',
                'delete' => 'Can delete employees but cannot create or edit',
                'full'   => 'Administrator level access with all permissions'
            ],
                'Roles' => [
        'view'   => 'Can only view roles and permissions',
        'create' => 'Can create new roles but cannot edit or delete',
        'edit'   => 'Can edit existing roles but cannot create or delete',
        'delete' => 'Can delete roles but cannot create or edit',
        'full'   => 'Administrator level access with all permissions',
    ],
    
            // Additional Modules
            'Dashboard' => [
                'view' => 'Can view dashboard data but cannot customize',
                'full' => 'Full access to dashboard with customization options'
            ],
            'Reports' => [
                'view' => 'Can view pre-generated reports',
                'create' => 'Can generate and view reports', // Using 'create' for generate
                'full' => 'Full access to all reporting features'
            ]
        ];

        $records = [];
        $now = now();

        foreach ($modules as $module => $accessLevels) {
            foreach ($accessLevels as $accessLevel => $description) {

                $exists = DB::table('module_access')
                    ->where('module', $module)
                    ->where('access_level', $accessLevel)
                    ->exists();

                if (!$exists) {
                    DB::table('module_access')->insert([
                        'access_id' => (string) \Illuminate\Support\Str::uuid(),
                        'module'      => $module,
                        'access_level'=> $accessLevel,
                        'description' => $description,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                        'deleted_at'  => null,
                    ]);
                }
            }
        }


        $this->command->info('Module access permissions seeded successfully!');
        $this->command->info('Total records inserted: ' . count($records));
    }
}