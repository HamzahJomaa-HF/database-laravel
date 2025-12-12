<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    public function up(): void
    {
        // Create a temporary table with the new structure
        Schema::create('rp_activity_mappings_new', function (Blueprint $table) {
            $table->uuid('rp_activity_mappings_id')->primary();
            $table->uuid('rp_activity_id');
            $table->uuid('main_activity_id');
            $table->timestamps();
            
            // Indexes
            $table->index('rp_activity_id');
            $table->index('main_activity_id');
            $table->unique(['rp_activity_id', 'main_activity_id'], 'unique_activity_mapping');
        });

        // Copy data from old table to new table
        DB::statement('
            INSERT INTO rp_activity_mappings_new (rp_activity_mappings_id, rp_activity_id, main_activity_id, created_at, updated_at)
            SELECT rp_activity_mappings_id, rp_activity_id, main_activity_id, created_at, updated_at
            FROM rp_activity_mappings
        ');

        // Drop old table
        Schema::dropIfExists('rp_activity_mappings');

        // Rename new table to old table name
        Schema::rename('rp_activity_mappings_new', 'rp_activity_mappings');
    }

    public function down(): void
    {
        // To rollback, you would need to recreate the original structure
        // This is more complex and may require restoring from backup
        // For simplicity, we'll just note that full rollback requires manual intervention
        throw new \Exception('This migration cannot be rolled back automatically. Use backup if needed.');
    }
};