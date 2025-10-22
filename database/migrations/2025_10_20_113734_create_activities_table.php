<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activities', function (Blueprint $table) {
            $table->id('activity_id');
            // $table->foreignId('parent_activity')->nullable()->constrained('activities')->nullOnDelete();
            $table->string('activity_title');
            $table->string('activity_type')->nullable();
            $table->text('content_network')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            // $table->foreignId('target_cop')->nullable()->constrained('cops')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activities');
    }
};
