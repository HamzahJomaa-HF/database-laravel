<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Get roles
        $superAdminRole = Role::where('role_name', 'Super Admin')->first();
        $hrManagerRole = Role::where('role_name', 'HR Manager')->first();
        $programManagerRole = Role::where('role_name', 'Program Manager')->first();
        $projectCoordinatorRole = Role::where('role_name', 'Project Coordinator')->first();
        $fieldOfficerRole = Role::where('role_name', 'Field Officer')->first();
        
        // Default password for all seeded users
        $defaultPassword = 'password123';
        
        $employees = [
            // Super Admin
            [
                'employee_id' => Str::uuid(),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'admin@hariri.org',
                'password' => Hash::make($defaultPassword),
                'phone_number' => '+96170000001',
                'employee_type' => 'permanent',
                'start_date' => now()->subYears(2),
                'role_id' => $superAdminRole->role_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // HR Manager
            [
                'employee_id' => Str::uuid(),
                'first_name' => 'Sarah',
                'last_name' => 'Johnson',
                'email' => 'hr@hariri.org',
                'password' => Hash::make($defaultPassword),
                'phone_number' => '+96170000002',
                'employee_type' => 'permanent',
                'start_date' => now()->subYear(),
                'role_id' => $hrManagerRole->role_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Program Manager
            [
                'employee_id' => Str::uuid(),
                'first_name' => 'Mohammed',
                'last_name' => 'Alami',
                'email' => 'programs@hariri.org',
                'password' => Hash::make($defaultPassword),
                'phone_number' => '+96170000003',
                'employee_type' => 'permanent',
                'start_date' => now()->subMonths(18),
                'role_id' => $programManagerRole->role_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Project Coordinator
            [
                'employee_id' => Str::uuid(),
                'first_name' => 'Layla',
                'last_name' => 'Hassan',
                'email' => 'projects@hariri.org',
                'password' => Hash::make($defaultPassword),
                'phone_number' => '+96170000004',
                'employee_type' => 'contract',
                'start_date' => now()->subMonths(6),
                'end_date' => now()->addYears(1),
                'role_id' => $projectCoordinatorRole->role_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
            // Field Officer
            [
                'employee_id' => Str::uuid(),
                'first_name' => 'Ahmed',
                'last_name' => 'Fawaz',
                'email' => 'field@hariri.org',
                'password' => Hash::make($defaultPassword),
                'phone_number' => '+96170000005',
                'employee_type' => 'contract',
                'start_date' => now()->subMonths(3),
                'role_id' => $fieldOfficerRole->role_id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
            
           
        ];
        
        foreach ($employees as $employee) {
            Employee::create($employee);
        }
        
        $this->command->info('✓ Employees seeded successfully!');
        $this->command->info('  Default password for all users: ' . $defaultPassword);
        $this->command->info('  Super Admin: admin@hariri.org / ' . $defaultPassword);
    }
}