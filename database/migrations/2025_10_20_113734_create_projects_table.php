<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->uuid('project_id')->primary();
            $table->string('name');
            $table->string('folder_name')->nullable()->after('name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('project_type')->nullable();
            $table->string('project_group')->nullable();
            $table->string('external_id')->unique()->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
