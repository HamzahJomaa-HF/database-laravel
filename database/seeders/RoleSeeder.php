<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            [
                'role_id' => Str::uuid(),
                'role_name' => 'Super Admin',
                'description' => 'Full system administrator',
            ],
            [
                'role_id' => Str::uuid(),
                'role_name' => 'HR Manager',
                'description' => 'Manages employees, users',
            ],
            [
                'role_id' => Str::uuid(),
                'role_name' => 'Program Manager',
                'description' => 'Manages programs, projects, and activities',
            ],
            [
                'role_id' => Str::uuid(),
                'role_name' => 'Project Coordinator',
                'description' => 'Coordinates specific projects and activities',
            ],
            [
                'role_id' => Str::uuid(),
                'role_name' => 'Field Officer',
                'description' => 'Field operations and data collection',
            ],
           
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
        
        $this->command->info('✓ Roles seeded successfully!');
    }
}