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
    $table->string('identification_id')->unique();
    $table->string('first_name');
    $table->string('middle_name')->nullable();
    $table->string('mother_name')->nullable();
    $table->string('last_name');
    $table->string('gender')->nullable();          
    $table->date('dob');
    $table->string('register_number')->nullable();
    $table->string('phone_number')->unique();
    $table->string('marital_status')->nullable();  
    $table->string('current_situation')->nullable(); 
    $table->string('passport_number')->nullable(); 
    $table->string('register_place')->nullable();

    $table->timestamps();
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
            $table->uuid('user_id')->nullable()->index(); // FK to users
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
        // Drop sessions first because it references users
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};
