<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run()
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "uuid-ossp"');

        $email = env('SUPER_ADMIN_EMAIL');
        $password = env('SUPER_ADMIN_PASSWORD');
        $roleName = 'Super Administrator';

        // ----------------------------------
        // 1. Get or create role
        // ----------------------------------
        $role = DB::table('roles')->where('role_name', $roleName)->first();

        if (!$role) {
            $roleId = Str::uuid();

            DB::table('roles')->insert([
                'role_id' => $roleId,
                'role_name' => $roleName,
                'description' => 'Super admin with full access to all modules and functionalities',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $roleId = $role->role_id;
        }

        // ----------------------------------
        // 2. Ensure full module access exists
        // ----------------------------------
        $fullAccessIds = DB::table('module_access')
            ->where('access_level', 'full')
            ->pluck('access_id');

        foreach ($fullAccessIds as $accessId) {
            $exists = DB::table('roles_module_access')
                ->where('role_id', $roleId)
                ->where('access_id', $accessId)
                ->exists();

            if (!$exists) {
                DB::table('roles_module_access')->insert([
                    'roles_module_access_id' => Str::uuid(),
                    'role_id' => $roleId,
                    'access_id' => $accessId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // ----------------------------------
        // 3. Get or create employee
        // ----------------------------------
        $employee = DB::table('employees')->where('email', $email)->first();

        if (!$employee) {
            $employeeId = Str::uuid();

            DB::table('employees')->insert([
                'employee_id' => $employeeId,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone_number' => null,
                'email' => $email,
                'employee_type' => 'Administrator',
                'start_date' => now()->toDateString(),
                'end_date' => null,
                'external_id' => null,
                'role_id' => $roleId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $employeeId = $employee->employee_id;
        }

        // ----------------------------------
        // 4. Get or create credentials
        // ----------------------------------
        $credentialsExists = DB::table('credentials_employees')
            ->where('employee_id', $employeeId)
            ->exists();

        if (!$credentialsExists) {
            DB::table('credentials_employees')->insert([
                'credentials_employees_id' => Str::uuid(),
                'employee_id' => $employeeId,
                'password_hash' => Hash::make($password),
                'remember_token' => null,
                'email_verified_at' => now(),
                'is_active' => true,
                'last_login_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Super admin seeder executed safely.');
        $this->command->info("Email: {$email}");
        $this->command->info('Role: Super Administrator');
    }
}
