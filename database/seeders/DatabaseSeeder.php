<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info("🚀 Starting database seeding process...");
        $this->command->info("=" . str_repeat("=", 50));
        
        // ============================================
        // PHASE 1: Reference/Lookup Tables
        // ============================================
        $this->command->info("\n📚 PHASE 1: Seeding Reference/Lookup Tables");
        $this->command->info("   " . str_repeat("-", 45));
        
        $this->call([
            DiplomaSeeder::class,
            NationalitySeeder::class,
        ]);
        
        $this->command->info("✅ Reference tables seeded!");
        
        // ============================================
        // PHASE 2: Core Foundation Data
        // ============================================
        $this->command->info("\n🏛️  PHASE 2: Seeding Core Foundation Data");
        $this->command->info("   " . str_repeat("-", 45));
        
        $this->call([
            ProgramSeeder::class,    // Programs first (parent)
            ProjectSeeder::class,    // Projects second (depends on programs)
        ]);
        
        $this->command->info("✅ Core foundation data seeded!");
        
        // ============================================
        // PHASE 3: Operational Data
        // ============================================
        $this->command->info("\n📊 PHASE 3: Seeding Operational Data");
        $this->command->info("   " . str_repeat("-", 45));
        
        $this->call([
            ActivitySeeder::class,   // Activities (depends on programs & projects)
        ]);
        
        $this->command->info("✅ Operational data seeded!");
        
        // ============================================
        // FINAL SUMMARY
        // ============================================
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("🎉 DATABASE SEEDING COMPLETED SUCCESSFULLY!");
        $this->command->info("" . str_repeat("=", 50));
        $this->command->info("📋 Summary of seeded data:");
        $this->command->info("   ✅ Diplomas: 12 entries");
        $this->command->info("   ✅ Nationalities: 16 entries");
        $this->command->info("   ✅ Programs: " . \App\Models\Program::count() . " entries");
        $this->command->info("   ✅ Projects: " . \App\Models\Project::count() . " entries");
        $this->command->info("   ✅ Activities: " . \App\Models\Activity::count() . " entries");
        $this->command->info("" . str_repeat("=", 50));
    }
}