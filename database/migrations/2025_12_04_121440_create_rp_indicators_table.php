<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_indicators', function (Blueprint $table) {
            $table->uuid('rp_indicators_id')->primary();
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('indicator_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('indicator_type')->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->decimal('baseline_value', 15, 2)->nullable();
            $table->date('baseline_date')->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->date('target_date')->nullable();
            $table->string('data_source')->nullable();
            $table->string('frequency')->nullable();
            $table->text('calculation_method')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_indicators');
    }
};