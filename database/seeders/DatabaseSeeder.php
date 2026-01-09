<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
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
            RoleSeeder::class,           // 1. Create roles
            PermissionSeeder::class,     // 2. Create permissions
            RolePermissionSeeder::class, // 3. Assign permissions to roles
            ProgramSeeder::class,        // 4. Programs (parent)
            ProjectSeeder::class,        // 5. Projects (depends on programs)
            EmployeeSeeder::class,       // 6. Employees (needs roles)
        ]);
        
        $this->command->info("✅ Core foundation data seeded!");
        
        // ============================================
        // PHASE 3: Security & Access Control
        // ============================================
        $this->command->info("\n🔐 PHASE 3: Seeding Security & Access Control");
        $this->command->info("   " . str_repeat("-", 45));
        
        $this->call([
            ModuleAccessSeeder::class,   // Module access for employees
        ]);
        
        $this->command->info("✅ Security data seeded!");
        
        // ============================================
        // PHASE 4: Operational Data
        // ============================================
        $this->command->info("\n📊 PHASE 4: Seeding Operational Data");
        $this->command->info("   " . str_repeat("-", 45));
        
        $this->call([
            ActivitySeeder::class,       // Activities (depends on programs & projects)
            // REMOVED: UserSeeder doesn't exist (you said you don't want it)
            // REMOVED: SurveySeeder doesn't exist
            // REMOVED: ActionPlanSeeder doesn't exist  
            // REMOVED: CopSeeder doesn't exist
            // REMOVED: PortfolioSeeder doesn't exist
        ]);
        
        $this->command->info("✅ Operational data seeded!");
        
        // ============================================
        // PHASE 5: Relationships & Pivot Data
        // ============================================
        $this->command->info("\n🔗 PHASE 5: Seeding Relationships & Pivot Data");
        $this->command->info("   " . str_repeat("-", 45));
        
        // REMOVE THESE - they don't exist:
        // UserDiplomaSeeder::class,
        // UserNationalitySeeder::class,    
        // PortfolioActivitySeeder::class,  
        // ProjectPortfolioSeeder::class,   
        // ProjectEmployeeSeeder::class,
        
        $this->command->info("⏭️  Skipped relationship seeders (not created)");
        
        // ============================================
        // FINAL SUMMARY
        // ============================================
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("🎉 DATABASE SEEDING COMPLETED SUCCESSFULLY!");
        $this->command->info("" . str_repeat("=", 50));
        
        // Display counts
        $this->command->info("📋 Summary of seeded data:");
        $this->command->info("   ✅ Roles: " . \App\Models\Role::count() . " entries");
        $this->command->info("   ✅ Permissions: " . \App\Models\Permission::count() . " entries");
        $this->command->info("   ✅ Employees: " . \App\Models\Employee::count() . " entries");
        $this->command->info("   ✅ Module Access: " . \App\Models\ModuleAccess::count() . " entries");
        $this->command->info("   ✅ Diplomas: " . \App\Models\Diploma::count() . " entries");
        $this->command->info("   ✅ Nationalities: " . \App\Models\Nationality::count() . " entries");
        $this->command->info("   ✅ Programs: " . \App\Models\Program::count() . " entries");
        $this->command->info("   ✅ Projects: " . \App\Models\Project::count() . " entries");
        $this->command->info("   ✅ Activities: " . \App\Models\Activity::count() . " entries");
        
        // Display login credentials
        $this->command->info("\n🔑 LOGIN CREDENTIALS FOR TESTING:");
        $this->command->info("   " . str_repeat("-", 45));
        $this->command->info("   👑 Super Admin: admin@hariri.org / password123");
        $this->command->info("   👩‍💼 HR Manager: hr@hariri.org / password123");
        $this->command->info("   👨‍💼 Program Manager: programs@hariri.org / password123");
        $this->command->info("   👩‍🔧 Project Coordinator: projects@hariri.org / password123");
        $this->command->info("   👨‍🌾 Field Officer: field@hariri.org / password123");
        $this->command->info("   👁️  Viewer: viewer@hariri.org / password123");
        
        $this->command->info("\n" . str_repeat("=", 50));
        $this->command->info("✨ Ready to test your application!");
        $this->command->info("" . str_repeat("=", 50));
    }
}