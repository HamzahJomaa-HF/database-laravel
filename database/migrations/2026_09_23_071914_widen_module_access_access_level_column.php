<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Per-button permissions need one access_level string per distinct action per
     * module (e.g. "activate", "force_delete", "download_template"), which no
     * longer fits a single shared CHECK-constraint enum. The valid set of keys is
     * now enforced by config/permissions.php instead.
     */
    public function up(): void
    {
        DB::statement("
            ALTER TABLE module_access
            DROP CONSTRAINT IF EXISTS module_access_access_level_check
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
            CHECK (access_level IN ('view', 'create', 'edit', 'delete', 'manage', 'full', 'export', 'import'))
        ");
    }
};
