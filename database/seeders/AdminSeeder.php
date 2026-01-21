<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Enable UUID generation if needed
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        // Generate UUIDs
        $employeeId = Str::uuid();
        $roleId = Str::uuid();
        $credentialsId = Str::uuid();
        
        // Get all "full" access IDs from module_access
        $fullAccessIds = DB::table('module_access')
            ->where('access_level', 'full')
            ->pluck('access_id')
            ->toArray();
        
        // 1. Create a role for super admin
        $roleData = [
            'role_id' => $roleId,
            'role_name' => 'Super Administrator',
            'description' => 'Super admin with full access to all modules and functionalities',
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        DB::table('roles')->insert($roleData);
        
        // 2. Create role-module access mappings for all full access modules
        $roleModuleAccessData = [];
        foreach ($fullAccessIds as $accessId) {
            $roleModuleAccessData[] = [
                'roles_module_access_id' => Str::uuid(),
                'role_id' => $roleId,
                'access_id' => $accessId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        DB::table('roles_module_access')->insert($roleModuleAccessData);
        
        // 3. Create the employee record
        $employeeData = [
            'employee_id' => $employeeId,
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'phone_number' => null,
            'email' => 'ayaantar@gmail.com',
            'employee_type' => 'Administrator',
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'external_id' => null,
            'role_id' => $roleId,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        DB::table('employees')->insert($employeeData);
        
        // 4. Create credentials for the employee
        $credentialsData = [
            'credentials_employees_id' => $credentialsId,
            'employee_id' => $employeeId,
            'password_hash' => Hash::make('123456789'),
            'remember_token' => null,
            'email_verified_at' => now(),
            'is_active' => true,
            'last_login_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
        
        DB::table('credentials_employees')->insert($credentialsData);
        
        $this->command->info('Super admin created successfully!');
        $this->command->info('Email: ayaantar@gmail.com');
        $this->command->info('Password: 123456789');
        $this->command->info('Role: Super Administrator with full access to all modules');
    }
}