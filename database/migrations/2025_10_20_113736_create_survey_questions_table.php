<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('survey_questions', function (Blueprint $table) {
               $table->uuid('survey_question_id')->primary();
            // $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            // $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->integer('question_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('survey_questions');
    }
};
