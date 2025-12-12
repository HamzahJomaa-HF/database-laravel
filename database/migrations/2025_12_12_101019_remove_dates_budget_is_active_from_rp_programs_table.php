<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_programs', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'budget', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('rp_programs', function (Blueprint $table) {
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};