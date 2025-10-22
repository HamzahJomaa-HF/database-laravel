<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('questions', function (Blueprint $table) {
            $table->id('question_id');
            // $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->enum('question_type', ['text', 'multiple_choice', 'checkbox', 'rating']);
            $table->string('question_name');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('questions');
    }
};
