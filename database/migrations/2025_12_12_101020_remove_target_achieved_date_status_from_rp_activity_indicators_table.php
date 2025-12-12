<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_activity_indicators', function (Blueprint $table) {
            $table->dropColumn([
                'target_value',
                'achieved_value',
                'achieved_date',
                'status'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rp_activity_indicators', function (Blueprint $table) {
            $table->decimal('target_value', 10, 2)->nullable();
            $table->decimal('achieved_value', 10, 2)->nullable();
            $table->date('achieved_date')->nullable();
            $table->string('status', 255)->nullable();
        });
    }
};