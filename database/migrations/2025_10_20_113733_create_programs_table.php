<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('programs', function (Blueprint $table) {
            $table->uuid('program_id')->primary();
            $table->string('name');
            $table->string('type');
            $table->string('program_type');
            $table->text('description')->nullable();
            $table->string('external_id')->unique()->nullable()->after('description');
            $table->timestamps();

        });
    }

    public function down(): void {
        Schema::dropIfExists('programs');
    }
};
