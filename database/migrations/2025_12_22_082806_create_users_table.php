<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('prefix', 255)->nullable();
            $table->boolean('is_high_profile')->default(false);
            $table->string('scope', 255);
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('gender', 255);
            $table->string('position_1', 255);
            $table->string('organization_1', 255);
            $table->string('organization_type_1', 255);
            $table->string('status_1', 255);
            $table->text('address');
            $table->string('phone_number', 255);
            $table->string('sector', 255)->nullable();
            $table->string('middle_name', 255)->nullable();
            $table->string('mother_name', 255)->nullable();
            $table->date('dob')->nullable();
            $table->string('office_phone', 255)->nullable();
            $table->string('extension_number', 255)->nullable();
            $table->string('home_phone', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('position_2', 255)->nullable();
            $table->string('organization_2', 255)->nullable();
            $table->string('organization_type_2', 255)->nullable();
            $table->string('status_2', 255)->nullable();
            $table->string('identification_id', 255)->nullable();
            $table->string('register_number', 255)->nullable();
            $table->string('marital_status', 255)->nullable();
            $table->string('employment_status', 255)->nullable();
            $table->string('passport_number', 255)->nullable();
            $table->string('register_place', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->uuid('default_cop_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};