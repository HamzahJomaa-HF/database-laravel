<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_activities', function (Blueprint $table) {
            $table->uuid('rp_activities_id')->primary();
            $table->uuid('rp_actions_id');
             $table->string('external_id', 255)->nullable();
            $table->string('external_type', 255)->nullable();
            $table->string('name', 255);
            $table->string('code', 255);
            $table->text('description')->nullable();
            $table->string('activity_type', 255)->nullable();
            $table->string('status', 255)->default('ongoing');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_activities');
    }
};