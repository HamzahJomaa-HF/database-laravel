<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_activities', function (Blueprint $table) {
            $table->uuid('project_activity_id')->primary();
            $table->string('external_id')->nullable()->unique();
            
            // ADD THESE REQUIRED COLUMNS:
            $table->uuid('project_id');  // REQUIRED
            $table->uuid('activity_id'); // REQUIRED
            
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_activities');
    }
};