<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_activity_indicators', function (Blueprint $table) {
            $table->uuid('rp_activity_indicators_id')->primary();
            $table->uuid('rp_activities_id');
            $table->uuid('rp_indicators_id');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_activity_indicators');
    }
};