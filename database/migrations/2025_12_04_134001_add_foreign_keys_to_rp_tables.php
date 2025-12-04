<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SIMPLE APPROACH: Just add the foreign keys without complex checks
        // Since we know tables exist (they were created in previous migrations)
        
        // 1. rp_programs → rp_components
        Schema::table('rp_programs', function (Blueprint $table) {
            $table->foreign('component_id', 'FK_RP_PROGRAMS_COMPONENT_ID')
                  ->references('rp_components_id')->on('rp_components')
                  ->onDelete('cascade');
        });

        // 2. rp_units → rp_programs
        Schema::table('rp_units', function (Blueprint $table) {
            $table->foreign('program_id', 'FK_RP_UNITS_PROGRAM_ID')
                  ->references('rp_programs_id')->on('rp_programs')
                  ->onDelete('cascade');
        });

        // 3. rp_actions → rp_units
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->foreign('unit_id', 'FK_RP_ACTIONS_UNIT_ID')
                  ->references('rp_units_id')->on('rp_units')
                  ->onDelete('cascade');
        });

        // 4. rp_activities → rp_actions
        Schema::table('rp_activities', function (Blueprint $table) {
            $table->foreign('action_id', 'FK_RP_ACTIVITIES_ACTION_ID')
                  ->references('rp_actions_id')->on('rp_actions')
                  ->onDelete('cascade');
        });

        // 5. rp_target_actions → rp_actions
        Schema::table('rp_target_actions', function (Blueprint $table) {
            $table->foreign('action_id', 'FK_RP_TARGET_ACTIONS_ACTION_ID')
                  ->references('rp_actions_id')->on('rp_actions')
                  ->onDelete('cascade');
        });

        // 6. rp_activity_indicators → rp_activities & rp_indicators
        Schema::table('rp_activity_indicators', function (Blueprint $table) {
            $table->foreign('activity_id', 'FK_RP_ACTIVITY_INDICATORS_ACTIVITY_ID')
                  ->references('rp_activities_id')->on('rp_activities')
                  ->onDelete('cascade');
            
            $table->foreign('indicator_id', 'FK_RP_ACTIVITY_INDICATORS_INDICATOR_ID')
                  ->references('rp_indicators_id')->on('rp_indicators')
                  ->onDelete('cascade');
        });

        // 7. rp_activity_focalpoints → rp_activities & rp_focalpoints
        Schema::table('rp_activity_focalpoints', function (Blueprint $table) {
            $table->foreign('activity_id', 'FK_RP_ACTIVITY_FOCALPOINTS_ACTIVITY_ID')
                  ->references('rp_activities_id')->on('rp_activities')
                  ->onDelete('cascade');
            
            $table->foreign('focalpoint_id', 'FK_RP_ACTIVITY_FOCALPOINTS_FOCALPOINT_ID')
                  ->references('rp_focalpoints_id')->on('rp_focalpoints')
                  ->onDelete('cascade');
        });

        // 8. rp_activity_mappings → rp_activities & activities
        Schema::table('rp_activity_mappings', function (Blueprint $table) {
            $table->foreign('rp_activity_id', 'FK_RP_ACTIVITY_MAPPINGS_RP_ACTIVITY_ID')
                  ->references('rp_activities_id')->on('rp_activities')
                  ->onDelete('cascade');
            
            $table->foreign('main_activity_id', 'FK_RP_ACTIVITY_MAPPINGS_MAIN_ACTIVITY_ID')
                  ->references('activity_id')->on('activities')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // Drop foreign keys in reverse order
        
        // 8. rp_activity_mappings
        Schema::table('rp_activity_mappings', function (Blueprint $table) {
            $table->dropForeign('FK_RP_ACTIVITY_MAPPINGS_RP_ACTIVITY_ID');
            $table->dropForeign('FK_RP_ACTIVITY_MAPPINGS_MAIN_ACTIVITY_ID');
        });

        // 7. rp_activity_focalpoints
        Schema::table('rp_activity_focalpoints', function (Blueprint $table) {
            $table->dropForeign('FK_RP_ACTIVITY_FOCALPOINTS_ACTIVITY_ID');
            $table->dropForeign('FK_RP_ACTIVITY_FOCALPOINTS_FOCALPOINT_ID');
        });

        // 6. rp_activity_indicators
        Schema::table('rp_activity_indicators', function (Blueprint $table) {
            $table->dropForeign('FK_RP_ACTIVITY_INDICATORS_ACTIVITY_ID');
            $table->dropForeign('FK_RP_ACTIVITY_INDICATORS_INDICATOR_ID');
        });

        // 5. rp_target_actions
        Schema::table('rp_target_actions', function (Blueprint $table) {
            $table->dropForeign('FK_RP_TARGET_ACTIONS_ACTION_ID');
        });

        // 4. rp_activities
        Schema::table('rp_activities', function (Blueprint $table) {
            $table->dropForeign('FK_RP_ACTIVITIES_ACTION_ID');
        });

        // 3. rp_actions
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->dropForeign('FK_RP_ACTIONS_UNIT_ID');
        });

        // 2. rp_units
        Schema::table('rp_units', function (Blueprint $table) {
            $table->dropForeign('FK_RP_UNITS_PROGRAM_ID');
        });

        // 1. rp_programs
        Schema::table('rp_programs', function (Blueprint $table) {
            $table->dropForeign('FK_RP_PROGRAMS_COMPONENT_ID');
        });
    }
};