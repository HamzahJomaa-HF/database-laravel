<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('program_id')->primary();
            $table->string('name', 255);
            $table->string('folder_name', 255)->nullable();
            $table->string('type', 255);
            $table->string('program_type', 255)->nullable();
            $table->text('description')->nullable();
             $table->string('external_id', 255)->nullable();
            $table->string('time', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->uuid('parent_program_id')->nullable(); // Column only, foreign key in separate file
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('programs');
    }
};