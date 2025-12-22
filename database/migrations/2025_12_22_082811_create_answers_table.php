<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->uuid('answer_id')->primary();
            $table->text('answer_value')->nullable();
            $table->string('external_id', 255)->nullable();
            $table->uuid('response_id'); // COLUMN ONLY
            $table->uuid('question_id'); // COLUMN ONLY
            $table->uuid('survey_question_id'); // COLUMN ONLY
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};