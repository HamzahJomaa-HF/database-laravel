<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('employee_id')->primary();
            $table->string('first_name', 255);
            $table->string('last_name', 255);
            $table->string('phone_number', 255)->nullable();
            $table->string('email', 255);
            $table->string('employee_type', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
             $table->string('external_id', 255)->nullable();
            $table->uuid('role_id')->nullable(); // COLUMN ONLY
            $table->uuid('project_id')->nullable(); // COLUMN ONLY
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};