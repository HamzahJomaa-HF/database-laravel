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
        Schema::create('roles_module_access', function (Blueprint $table) {
            // Primary Key
            $table->uuid('roles_module_access_id')->primary();
            
            // Foreign Key to roles table
            $table->uuid('role_id');
            $table->foreign('role_id')
                  ->references('role_id')
                  ->on('roles')
                  ->onDelete('cascade');
            
            // Foreign Key to module_access table
            $table->uuid('access_id');
            $table->foreign('access_id')
                  ->references('access_id')
                  ->on('module_access')
                  ->onDelete('cascade');
            
            // Timestamps
            $table->timestamps();
            
            // Soft delete
            $table->softDeletes();
            
            // Composite unique constraint - a role can't have the same access twice
            $table->unique(['role_id', 'access_id', 'deleted_at'], 'role_access_unique');
            
            // Indexes for better performance
            $table->index(['role_id']);
            $table->index(['access_id']);
            $table->index(['deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles_module_access');
    }
};