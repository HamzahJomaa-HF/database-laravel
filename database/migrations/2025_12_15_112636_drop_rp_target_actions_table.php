<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('rp_target_actions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('rp_target_actions', function (Blueprint $table) {
            $table->uuid('rp_target_actions_id')->primary();
            $table->uuid('action_id');
            $table->uuid('external_id')->nullable();
            $table->string('target_name', 255);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Add foreign key constraint if needed
            // $table->foreign('action_id')->references('rp_actions_id')->on('rp_actions');
        });
    }
};