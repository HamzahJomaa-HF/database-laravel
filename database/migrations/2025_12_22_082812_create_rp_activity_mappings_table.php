<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_activity_mappings', function (Blueprint $table) {
            $table->uuid('rp_activity_mappings_id')->primary();
            $table->uuid('rp_activities_id');
            $table->uuid('activity_id');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_activity_mappings');
    }
};