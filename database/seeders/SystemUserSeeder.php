<?php

namespace Database\Seeders;

use App\Models\SystemUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user with your credentials
        SystemUser::create([
            'name' => 'System Administrator',
            'email' => 'ayaantar315@gmail.com',
            'username' => 'admin',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: ayaantar315@gmail.com');
        $this->command->info('Password: 123456789');
        $this->command->info('Username: admin');
    }
}