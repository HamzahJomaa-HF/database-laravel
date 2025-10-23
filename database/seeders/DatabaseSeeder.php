<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Quick test user (no FK for role)
        User::factory()->create([
            'first_name' => 'Test',
            'last_name' => 'User',
            // 'user_role' => 'some-role-uuid', // REMOVE for now
        ]);
    }
}
