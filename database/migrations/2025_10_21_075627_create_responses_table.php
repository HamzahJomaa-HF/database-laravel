<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('responses', function (Blueprint $table) {
            $table->uuid('response_id')->primary();
            // $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // $table->foreignId('survey_id')->constrained('surveys')->cascadeOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('responses');
    }


   
};
