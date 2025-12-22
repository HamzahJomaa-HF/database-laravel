<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_focalpoints', function (Blueprint $table) {
            $table->uuid('rp_focalpoints_id')->primary();
             $table->string('external_id', 255)->nullable();
            $table->string('name', 255);
            $table->string('type', 255)->default('rp_activity');
            $table->uuid('employee_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_focalpoints');
    }
};