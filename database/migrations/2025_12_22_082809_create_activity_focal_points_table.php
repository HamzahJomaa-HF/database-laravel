<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_focal_points', function (Blueprint $table) {
            $table->uuid('activity_focal_point_id')->primary();
            $table->uuid('activity_id'); // COLUMN ONLY
            $table->uuid('rp_focalpoints_id'); // COLUMN ONLY
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_focal_points');
    }
};