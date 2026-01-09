// database/migrations/xxxx_create_module_access_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('module_access', function (Blueprint $table) {
            $table->uuid('access_id')->primary();
            $table->foreignUuid('employee_id')->references('employee_id')->on('employees')->onDelete('cascade');
            
            // Module type: 'activities', 'users', 'projects', 'programs', 'action_plans', 'surveys', 'reports', 'all'
            $table->string('module')->index();
            
            // Resource ID (optional - for specific resource access)
            $table->uuid('resource_id')->nullable()->index();
            
            // Resource type (for polymorphic relation)
            $table->string('resource_type')->nullable()->index();
            
            // Access level: 'none', 'view', 'create', 'edit', 'delete', 'manage'
            $table->enum('access_level', ['none', 'view', 'create', 'edit', 'delete', 'manage', 'full'])->default('view');
            
            // Timestamps
            $table->timestamps();
            
            // Unique constraint: one access record per employee-module-resource
            $table->unique(['employee_id', 'module', 'resource_id', 'resource_type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('module_access');
    }
};