<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // RP Actions foreign key
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->foreign('rp_units_id')
                  ->references('rp_units_id')
                  ->on('rp_units')
                  ->onDelete('cascade');
        });

        // RP Activities foreign key
        Schema::table('rp_activities', function (Blueprint $table) {
            $table->foreign('rp_actions_id')
                  ->references('rp_actions_id')
                  ->on('rp_actions')
                  ->onDelete('cascade');
        });

        // RP Activity focalpoints foreign keys
        Schema::table('rp_activity_focalpoints', function (Blueprint $table) {
            $table->foreign('rp_activities_id')
                  ->references('rp_activities_id')
                  ->on('rp_activities')
                  ->onDelete('cascade');
                  
            $table->foreign('rp_focalpoints_id')
                  ->references('rp_focalpoints_id')
                  ->on('rp_focalpoints')
                  ->onDelete('cascade');
        });

        // RP Activity indicators foreign keys
        Schema::table('rp_activity_indicators', function (Blueprint $table) {
            $table->foreign('rp_activities_id')
                  ->references('rp_activities_id')
                  ->on('rp_activities')
                  ->onDelete('cascade');
                  
            $table->foreign('rp_indicators_id')
                  ->references('rp_indicators_id')
                  ->on('rp_indicators')
                  ->onDelete('cascade');
        });

        // RP Activity mappings foreign keys
        Schema::table('rp_activity_mappings', function (Blueprint $table) {
            $table->foreign('rp_activities_id')
                  ->references('rp_activities_id')
                  ->on('rp_activities')
                  ->onDelete('cascade');
                  
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
        });

        // RP Focalpoints foreign key
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->onDelete('set null');
        });

        // RP Programs foreign key
        Schema::table('rp_programs', function (Blueprint $table) {
            $table->foreign('rp_components_id')
                  ->references('rp_components_id')
                  ->on('rp_components')
                  ->onDelete('cascade');
        });

        // RP Units foreign key
        Schema::table('rp_units', function (Blueprint $table) {
            $table->foreign('rp_programs_id')
                  ->references('rp_programs_id')
                  ->on('rp_programs')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Drop foreign keys in reverse order
        $tables = [
            'rp_units',
            'rp_programs',
            'rp_focalpoints',
            'rp_activity_mappings',
            'rp_activity_indicators',
            'rp_activity_focalpoints',
            'rp_activities',
            'rp_actions'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $foreignKeys = Schema::getConnection()
                    ->getDoctrineSchemaManager()
                    ->listTableForeignKeys($table->getTable());
                    
                foreach ($foreignKeys as $foreignKey) {
                    $table->dropForeign($foreignKey->getName());
                }
            });
        }
    }
};