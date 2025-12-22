<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cops', function (Blueprint $table) {
            $table->uuid('cop_id')->primary();
            $table->string('cop_name', 255);
            $table->text('description')->nullable();
             $table->string('external_id', 255)->nullable();
            $table->uuid('program_id'); // COLUMN ONLY
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cops');
    }
};