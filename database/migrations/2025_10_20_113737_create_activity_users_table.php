<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activity_users', function (Blueprint $table) {
            $table->id('activity_user_id');
            // $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            // $table->foreignId('activity_id')->constrained('activities')->cascadeOnDelete();
            $table->boolean('is_lead')->default(false);
            $table->boolean('invited')->default(false);
            $table->boolean('attended')->default(false);
            // $table->foreignId('cop_id')->nullable()->constrained('cops')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activity_users');
    }
};
