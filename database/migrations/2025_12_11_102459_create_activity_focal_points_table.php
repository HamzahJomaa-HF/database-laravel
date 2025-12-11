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
            $table->uuid('activity_id');
            $table->uuid('focal_point_id');
            $table->timestamps();
            
            // Foreign key to activities table
            $table->foreign('activity_id', 'FK_ACTIVITY_FOCAL_POINTS_ACTIVITY_ID')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('cascade');
            
            // Foreign key to rp_focalpoints (renamed/used as general focal points)
            $table->foreign('focal_point_id', 'FK_ACTIVITY_FOCAL_POINTS_FOCAL_POINT_ID')
                  ->references('rp_focalpoints_id')
                  ->on('rp_focalpoints')
                  ->onDelete('cascade');
            
            // Prevent duplicate associations
            $table->unique(['activity_id', 'focal_point_id'], 'UNIQUE_ACTIVITY_FOCAL_POINT');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_focal_points');
    }
};