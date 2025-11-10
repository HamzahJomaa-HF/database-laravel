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
       Schema::create('users_diploma', function (Blueprint $table) {
    $table->id();
    $table->uuid('user_id');
    $table->unsignedBigInteger('diploma_id');
    $table->timestamps();

    $table->unique(['user_id', 'diploma_id']); 
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_diploma');
    }
};
