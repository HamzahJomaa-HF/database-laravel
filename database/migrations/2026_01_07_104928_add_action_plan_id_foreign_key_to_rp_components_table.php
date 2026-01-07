<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First, add the action_plan_id column to rp_components if it doesn't exist
        if (!Schema::hasColumn('rp_components', 'action_plan_id')) {
            Schema::table('rp_components', function (Blueprint $table) {
                $table->uuid('action_plan_id')->nullable()->after('rp_components_id');
            });
        }
        
        // Add foreign key constraint from rp_components to action_plans
        Schema::table('rp_components', function (Blueprint $table) {
            $table->foreign('action_plan_id')
                  ->references('action_plan_id')
                  ->on('action_plans')
                  ->onDelete('set null'); // or 'cascade' based on your needs
        });
    }

    public function down(): void
    {
        Schema::table('rp_components', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['action_plan_id']);
            
            // Then drop the column
            $table->dropColumn('action_plan_id');
        });
    }
};