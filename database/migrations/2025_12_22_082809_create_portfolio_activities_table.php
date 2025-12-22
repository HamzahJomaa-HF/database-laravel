<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_activities', function (Blueprint $table) {
            $table->uuid('portfolio_id');
            $table->uuid('activity_id');
            $table->timestamps();
            
            // Composite primary key
            $table->primary(['portfolio_id', 'activity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_activities');
    }
};