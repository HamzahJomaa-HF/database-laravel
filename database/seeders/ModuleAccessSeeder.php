<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleAccessSeeder extends Seeder
{
    /**
     * Seed one module_access row per (module, access_level) permission key
     * defined in config/permissions.php. This is additive/idempotent — existing
     * rows (and the role assignments pointing at them) are never touched, so
     * running this after adding a new button/permission only inserts the new rows.
     */
    public function run(): void
    {
        $modules = config('permissions');

        $now = now();
        $insertCount = 0;

        foreach ($modules as $module => $accessLevels) {
            foreach ($accessLevels as $accessLevel => $description) {
                $exists = DB::table('module_access')
                    ->where('module', $module)
                    ->where('access_level', $accessLevel)
                    ->exists();

                if (!$exists) {
                    DB::table('module_access')->insert([
                        'access_id' => (string) \Illuminate\Support\Str::uuid(),
                        'module' => $module,
                        'access_level' => $accessLevel,
                        'description' => $description,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'deleted_at' => null,
                    ]);
                    $insertCount++;
                }
            }
        }

        $this->command->info('Module access permissions seeded successfully!');
        $this->command->info('Total records inserted: ' . $insertCount);

        $this->command->info("\n=== MODULES SEEDED ===");
        foreach ($modules as $module => $accessLevels) {
            $this->command->line("- {$module} (" . count($accessLevels) . " permissions)");
        }
    }
}
