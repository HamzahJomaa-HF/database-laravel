<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE module_access
            DROP CONSTRAINT IF EXISTS module_access_access_level_check
        ");

        DB::statement("
            ALTER TABLE module_access
            ADD CONSTRAINT module_access_access_level_check
            CHECK (access_level IN ('view', 'create', 'edit', 'delete', 'manage', 'full', 'export'))
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE module_access
            DROP CONSTRAINT IF EXISTS module_access_access_level_check
        ");

        DB::statement("
            ALTER TABLE module_access
            ADD CONSTRAINT module_access_access_level_check
            CHECK (access_level IN ('view', 'create', 'edit', 'delete', 'manage', 'full'))
        ");
    }
};