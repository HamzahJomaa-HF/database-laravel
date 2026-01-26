<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            // Check if column exists before dropping it
            if (Schema::hasColumn('action_plans', 'rp_components_id')) {
                $table->dropColumn('rp_components_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('action_plans', function (Blueprint $table) {
            // Add the column back only if it doesn't exist
            if (!Schema::hasColumn('action_plans', 'rp_components_id')) {
                $table->uuid('rp_components_id')->nullable()->after('external_id');
            }
        });
    }
};