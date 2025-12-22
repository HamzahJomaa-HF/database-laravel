<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('diploma', function (Blueprint $table) {
            $table->uuid('diploma_id')->primary();
            $table->string('diploma_name', 255);
            $table->string('institution', 255)->nullable();
            $table->integer('year')->default(0)->nullable();
            $table->string('external_id', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('diploma');
    }
};