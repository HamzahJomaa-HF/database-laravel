<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cops', function (Blueprint $table) {
            $table->uuid('cop_id')->primary();
            // $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->string('cop_name');
            $table->text('description')->nullable();
            $table->string('external_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cops');
    }
};
