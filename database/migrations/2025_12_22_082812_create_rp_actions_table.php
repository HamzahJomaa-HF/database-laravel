<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rp_actions', function (Blueprint $table) {
            $table->uuid('rp_actions_id')->primary();
            $table->uuid('rp_units_id');
            $table->string('external_id', 255)->nullable();
            $table->string('name', 255);
            $table->string('code', 255);
            $table->text('objectives')->nullable();
            $table->date('planned_start_date')->nullable();
            $table->date('planned_end_date')->nullable();
            $table->text('targets_beneficiaries')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rp_actions');
    }
};