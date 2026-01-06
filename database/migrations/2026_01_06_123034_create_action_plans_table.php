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
       Schema::create('action_plans', function (Blueprint $table) {
    $table->uuid('action_plan_id')->primary();
    $table->string('title')->nullable();
    $table->date('start_date')->nullable();
    $table->date('end_date')->nullable();
    $table->string('external_id', 255)->nullable(); 
    $table->uuid('rp_components_id')->nullable(); 
    $table->string('excel_path')->nullable();
    $table->string('excel_filename')->nullable();
    $table->json('excel_metadata')->nullable(); 
    $table->timestamp('excel_uploaded_at')->nullable();
    $table->timestamp('excel_processed_at')->nullable();
    $table->timestamps();

});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_plans');
    }
};
