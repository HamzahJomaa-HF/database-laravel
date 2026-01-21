<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_employees', function (Blueprint $table) {
            $table->uuid('project_employee_id')->primary();
            $table->text('description')->nullable();
            $table->string('external_id', 255)->nullable();
            $table->uuid('program_id');//we took this out in this migration modify_employee_project_relations
            $table->uuid('employee_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_employees');
    }
};