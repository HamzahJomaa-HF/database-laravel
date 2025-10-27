<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('surveys', function (Blueprint $table) {
            $table->uuid('survey_id')->primary();
            $table->text('description')->nullable();
            $table->string('link')->nullable();
            $table->boolean('is_active')->default(true);
             $table->string('external_id')->unique()->nullable(false);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('surveys');
    }
};

