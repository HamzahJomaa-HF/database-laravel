<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_activity_focalpoints', function (Blueprint $table) {
            $table->uuid('rp_activity_focalpoints_id')->primary();
            $table->uuid('activity_id');
            $table->uuid('focalpoint_id');
            $table->string('role')->nullable();
            $table->text('responsibilities')->nullable();
            $table->date('assigned_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();
            
        
            
            // Keep the unique constraint
            $table->unique(['activity_id', 'focalpoint_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_activity_focalpoints');
    }
};