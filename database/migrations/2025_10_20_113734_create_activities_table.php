<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('activities', function (Blueprint $table) {
           $table->uuid('activity_id')->primary();
            $table->string('external_id')->unique(); 
            $table->string('folder_name')->nullable()->after('name');
            $table->string('activity_title_en')->nullable();
            $table->string('activity_title_ar')->nullable();
            $table->string('activity_type')->nullable();
            $table->text('content_network')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('activities');
    }
};
