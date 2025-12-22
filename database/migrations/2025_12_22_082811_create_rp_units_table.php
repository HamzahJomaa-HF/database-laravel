<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_units', function (Blueprint $table) {
            $table->uuid('rp_units_id')->primary();
            $table->uuid('rp_programs_id');
            $table->string('external_id', 255)->nullable();
            $table->string('name', 255);
            $table->string('code', 255);
            $table->string('unit_type', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_units');
    }
};