<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_actions', function (Blueprint $table) {
            $table->uuid('rp_actions_id')->primary();
            $table->uuid('unit_id');
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->string('status')->default('planned');
            $table->decimal('allocated_budget', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_actions');
    }
};