<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_diploma', function (Blueprint $table) {
            $table->uuid('user_id');
            $table->uuid('diploma_id');
            $table->timestamps();
            
            // Composite primary key
            $table->primary(['user_id', 'diploma_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_diploma');
    }
};