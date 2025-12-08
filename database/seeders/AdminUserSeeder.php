<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\SystemUser;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Create super admin for OTP testing
        $admin = SystemUser::updateOrCreate(
            ['email' => 'ayaantar315@gmail.com'], // Your testing email
            [
                'first_name' => 'Aya',
                'last_name' => 'Antar',
                'name' => 'Aya Antar',
                'password' => Hash::make('AdminPass123!'), // Better password
                'role' => 'admin',
                'phone' => '+1234567890', // Use your actual phone for SMS OTP testing
                'is_active' => true,
            ]
        );

        if ($admin->wasRecentlyCreated) {
            $this->command->info('✅ Admin user created successfully!');
            $this->command->line('');
            $this->command->info('=== TEST ADMIN CREDENTIALS ===');
            $this->command->info('Email: ayaantar315@gmail.com');
            $this->command->info('Password: AdminPass123!');
            $this->command->info('Role: admin');
            $this->command->info('Phone: +1234567890');
            $this->command->info('=============================');
            $this->command->line('');
            $this->command->warn('⚠️ FOR DEVELOPMENT ONLY');
            $this->command->warn('⚠️ Change credentials before production!');
        } else {
            $this->command->info('Admin user already exists - updated if needed.');
        }
        
        // Optional: Create test users for OTP testing
        $testUsers = [
            [
                'first_name' => 'Test',
                'last_name' => 'User1',
                'email' => 'testuser1@example.com',
                'phone' => '+1111111111',
                'role' => 'user'
            ],
            [
                'first_name' => 'Test',
                'last_name' => 'User2',
                'email' => 'testuser2@example.com',
                'phone' => '+2222222222',
                'role' => 'user'
            ],
        ];
        
        foreach ($testUsers as $userData) {
            SystemUser::updateOrCreate(
                ['email' => $userData['email']],
                array_merge($userData, [
                    'name' => $userData['first_name'] . ' ' . $userData['last_name'],
                    'password' => Hash::make('UserPass123!'),
                    'is_active' => true,
                ])
            );
        }
        
        $this->command->info('Test users created for OTP testing.');
    }
}