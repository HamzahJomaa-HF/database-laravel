<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove project_id from employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        // Remove program_id from project_employees table and add project_id
        Schema::table('project_employees', function (Blueprint $table) {
            // Drop the foreign key constraint for program_id
            $table->dropForeign(['program_id']);
            
            // Remove the program_id column
            $table->dropColumn('program_id');
            
            // Add project_id column
            $table->uuid('project_id')->nullable()->after('employee_id');
            
            // Add foreign key constraint for project_id
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Reverse the changes
        Schema::table('project_employees', function (Blueprint $table) {
            // Drop the project_id foreign key and column
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
            
            // Add program_id back
            $table->uuid('program_id')->nullable()->after('employee_id');
            
            // Add foreign key constraint for program_id
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('cascade');
        });

        // Add project_id back to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->uuid('project_id')->nullable()->after('role_id');
            
            $table->foreign('project_id')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('set null');
        });
    }
};