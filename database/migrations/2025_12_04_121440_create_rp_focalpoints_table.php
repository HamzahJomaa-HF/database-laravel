<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_focalpoints', function (Blueprint $table) {
            $table->uuid('rp_focalpoints_id')->primary();
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('focalpoint_code')->unique();
            $table->string('name');
            $table->string('position')->nullable();
            $table->string('department')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('responsibility_level')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_focalpoints');
    }
};