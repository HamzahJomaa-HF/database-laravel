<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_employees', function (Blueprint $table) {
            $table->id('project_employee_id');
            // $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            // $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_employees');
    }
};
