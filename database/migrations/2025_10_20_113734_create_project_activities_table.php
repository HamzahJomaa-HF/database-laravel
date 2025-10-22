<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('project_activities', function (Blueprint $table) {
            $table->id('project_activity_id');
            // $table->foreignId('project_center_id')->constrained('project_centers')->cascadeOnDelete();
            // $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('project_activities');
    }
};
