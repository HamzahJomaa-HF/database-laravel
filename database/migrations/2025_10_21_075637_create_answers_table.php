<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('answers', function (Blueprint $table) {
            $table->uuid('answer_id')->primary();

            // $table->foreignId('response_id')->constrained('responses')->cascadeOnDelete();
            // $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            // $table->foreignId('survey_question_id')->constrained('survey_questions')->cascadeOnDelete();
            $table->text('answer_value')->nullable();
            $table->string('external_id')->nullable()->unique()->after('description');

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('answers');
    }
};