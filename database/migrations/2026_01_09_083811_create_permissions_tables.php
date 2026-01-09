// database/migrations/xxxx_create_permissions_tables.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // 1. Permissions table
        Schema::create('permissions', function (Blueprint $table) {
            $table->uuid('permission_id')->primary();
            $table->string('name')->unique(); // e.g., 'view-action-plans', 'edit-activities'
            $table->string('group'); // e.g., 'action_plans', 'activities', 'reports'
            $table->string('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Role-Permission pivot table (Many-to-Many)
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->uuid('role_permission_id')->primary();
            $table->foreignUuid('role_id')->references('role_id')->on('roles')->onDelete('cascade');
            $table->foreignUuid('permission_id')->references('permission_id')->on('permissions')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['role_id', 'permission_id']);
        });

        // 3. Employee-Permission pivot table (for overriding role permissions)
        Schema::create('employee_permissions', function (Blueprint $table) {
            $table->uuid('employee_permission_id')->primary();
            $table->foreignUuid('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreignUuid('permission_id')->references('permission_id')->on('permissions')->onDelete('cascade');
            $table->boolean('is_granted')->default(true); // true=grant, false=deny (override)
            $table->timestamps();
            
            $table->unique(['employee_id', 'permission_id']);
        });

        // 4. Employee-Project access (for your "specific directory access" requirement)
        Schema::create('employee_project_access', function (Blueprint $table) {
            $table->uuid('access_id')->primary();
            $table->foreignUuid('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreignUuid('project_id')->references('project_id')->on('projects')->onDelete('cascade');
            $table->string('access_level')->default('view'); // view, edit, manage
            $table->timestamps();
            
            $table->unique(['employee_id', 'project_id']);
        });

        // 5. Employee-Program access (for program-level directory access)
        Schema::create('employee_program_access', function (Blueprint $table) {
            $table->uuid('access_id')->primary();
            $table->foreignUuid('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            $table->foreignUuid('program_id')->references('program_id')->on('programs')->onDelete('cascade');
            $table->string('access_level')->default('view');
            $table->timestamps();
            
            $table->unique(['employee_id', 'program_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_program_access');
        Schema::dropIfExists('employee_project_access');
        Schema::dropIfExists('employee_permissions');
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};