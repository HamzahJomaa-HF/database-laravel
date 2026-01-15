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
        Schema::create('credentials_employees', function (Blueprint $table) {
            // Primary Key
            $table->uuid('credentials_employees_id')->primary();
            
            // Foreign Key to employees table
            $table->uuid('employee_id')->unique();
            $table->foreign('employee_id')
                  ->references('employee_id')
                  ->on('employees')
                  ->onDelete('cascade');
            
            // Authentication fields
            $table->string('password_hash');
            $table->string('remember_token')->nullable();
            
            // Email verification status
            $table->timestamp('email_verified_at')->nullable();
            
            // Account status
            $table->boolean('is_active')->default(true);
            
            // Last login tracking
            $table->timestamp('last_login_at')->nullable();
            
            // Timestamps
            $table->timestamps();
            
            // Soft delete
            $table->softDeletes();
            
            // Optional: Indexes for better performance
            $table->index(['employee_id']);
            $table->index(['is_active']);
            $table->index(['email_verified_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credentials_employees');
    }
};