<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->uuid('activity_id')->primary();
             $table->string('external_id', 255)->nullable();
            $table->string('folder_name', 255)->nullable();
            $table->string('activity_title_en', 255)->nullable();
            $table->string('activity_title_ar', 255)->nullable();
            $table->string('activity_type', 255)->nullable();
            $table->text('content_network')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('parent_activity')->nullable(); // COLUMN ONLY
            $table->uuid('target_cop')->nullable(); // COLUMN ONLY
            $table->json('operational_support')->nullable();
            $table->string('venue', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};