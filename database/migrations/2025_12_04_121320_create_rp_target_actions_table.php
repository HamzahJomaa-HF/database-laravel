<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_target_actions', function (Blueprint $table) {
            $table->uuid('rp_target_actions_id')->primary();
            $table->uuid('action_id');
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('target_name');
            $table->text('description')->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->date('target_date')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('achieved_value', 15, 2)->nullable();
            $table->date('achieved_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_target_actions');
    }
};