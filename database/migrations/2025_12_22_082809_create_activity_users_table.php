<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_users', function (Blueprint $table) {
            $table->uuid('activity_user_id')->primary();
            $table->string('type', 255)->nullable();
            $table->boolean('is_lead')->default(false);
            $table->boolean('invited')->default(false);
            $table->boolean('attended')->default(false);
            $table->string('external_id', 255)->nullable();
            $table->uuid('user_id'); // COLUMN ONLY
            $table->uuid('activity_id'); // COLUMN ONLY
            $table->uuid('cop_id')->nullable(); // COLUMN ONLY
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_users');
    }
};