<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_units', function (Blueprint $table) {
            $table->uuid('rp_units_id')->primary();
            $table->uuid('program_id');
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('name');
            $table->string('code')->unique();
            $table->string('unit_type')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_units');
    }
};