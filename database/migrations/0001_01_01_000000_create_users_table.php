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
        // Users table
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            
            // Required Fields (X)
            $table->string('prefix')->nullable(); // Prefix (if available) - X but nullable if not available
            $table->boolean('is_high_profile')->default(false); // High Profile (Yes/No) - X
            $table->enum('scope', ['International', 'Regional', 'National', 'Local']); // Scope - X
            $table->string('community_of_practice'); // Community of Practice - X
            $table->string('first_name'); // First Name - X
            $table->string('last_name'); // Last Name - X (changed from family_name)
            $table->enum('gender', ['Male', 'Female', 'Other']); // Gender - X
            $table->string('position_1'); // Position 1 - X
            $table->string('organization_1'); // Organization 1 - X
            $table->enum('organization_type_1', [
                'Public Sector', 
                'Private Sector', 
                'Academia', 
                'UN', 
                'INGOs', 
                'Civil Society', 
                'NGOs', 
                'Activist'
            ]); // Type of Organization 1 - X
            $table->string('status_1'); // Status 1 - X
            $table->text('address'); // Address - X
            $table->string('mobile_phone'); // Mobile Phone Number - X
            
            // Optional Fields (optional/--)
            $table->string('sector')->nullable(); // Sector - optional
            $table->string('middle_name')->nullable(); // Middle Name - optional
            $table->string('mother_name')->nullable(); // Mother's Name - optional
            $table->date('dob')->nullable(); // Date of Birth - optional
            $table->string('office_phone')->nullable(); // Office Number - optional
            $table->string('extension_number')->nullable(); // Extension Number - optional
            $table->string('home_phone')->nullable(); // Home Number - optional
            $table->string('email')->unique()->nullable(); // Email - optional
            
            // Optional Secondary Position Fields
            $table->string('position_2')->nullable(); // Position 2 - optional
            $table->string('organization_2')->nullable(); // Organization 2 - optional
            $table->enum('organization_type_2', [
                'Public Sector', 
                'Private Sector', 
                'Academia', 
                'UN', 
                'INGOs', 
                'Civil Society', 
                'NGOs', 
                'Activist'
            ])->nullable(); // Type of Organization 2 - optional
            $table->string('status_2')->nullable(); // Status 2 - optional
            
            // Existing fields from original migration (keeping for backward compatibility)
            $table->string('identification_id')->nullable()->index();
            $table->string('register_number')->nullable();
            $table->string('phone_number')->nullable()->index(); // Keeping for backward compatibility
            $table->string('marital_status')->nullable()->index();
            $table->string('employment_status')->nullable()->index();
            $table->string('passport_number')->nullable();
            $table->string('register_place')->nullable();
            $table->string('type')->nullable()->index();
            
            $table->timestamps();
            
            // Add indexes for frequently searched fields
            $table->index(['first_name', 'last_name']); // Changed from family_name to last_name
            $table->index('community_of_practice');
            $table->index('sector');
            $table->index('organization_1');
            $table->index('organization_type_1');
            $table->index('mobile_phone');
        });

        // Password reset tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // Sessions table
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->uuid('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();

            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};