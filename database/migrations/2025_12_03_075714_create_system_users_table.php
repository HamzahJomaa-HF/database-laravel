<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->boolean('is_active')->default(true);
            
            // OTP fields
            $table->string('otp', 6)->nullable(); // Limit to 6 characters
            $table->timestamp('otp_expires_at')->nullable();
            $table->boolean('otp_enabled')->default(false);
            $table->enum('auth_method', ['password', 'otp'])->default('password');
            
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes(); // <-- ADD THIS for safe deletion
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_users');
    }
};