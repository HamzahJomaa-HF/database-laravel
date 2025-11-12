<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('diploma', function (Blueprint $table) {
    $table->uuid('diploma_id')->primary();
    $table->string('diploma_name');
    $table->string('institution')->nullable();
    $table->year('year')->nullable();
    $table->string('external_id')->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diploma');
    }
};
