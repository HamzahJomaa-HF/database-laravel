<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_indicators', function (Blueprint $table) {
            $table->uuid('rp_indicators_id')->primary();
             $table->string('external_id', 255)->nullable();
            $table->string('name', 255);
            $table->text('description')->nullable();
            $table->string('indicator_type', 255)->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->string('data_source', 255)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_indicators');
    }
};