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

        
        
    }

    public function down()
    {
      
        Schema::dropIfExists('role_permissions');
        Schema::dropIfExists('permissions');
    }
};