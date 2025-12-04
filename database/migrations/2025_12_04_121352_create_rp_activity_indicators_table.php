<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_activity_indicators', function (Blueprint $table) {
            $table->uuid('rp_activity_indicators_id')->primary();
            $table->uuid('activity_id');
            $table->uuid('indicator_id');
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('achieved_value', 15, 2)->nullable();
            $table->date('achieved_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
            
           
            $table->unique(['activity_id', 'indicator_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_activity_indicators');
    }
};