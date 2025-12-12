<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_target_actions', function (Blueprint $table) {
            $table->dropColumn([
                'target_value',
                'unit_of_measure',
                'target_date',
                'status',
                'achieved_value',
                'achieved_date'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rp_target_actions', function (Blueprint $table) {
            $table->decimal('target_value', 10, 2)->nullable();
            $table->string('unit_of_measure', 255)->nullable();
            $table->date('target_date')->nullable();
            $table->string('status', 255)->nullable();
            $table->decimal('achieved_value', 10, 2)->nullable();
            $table->date('achieved_date')->nullable();
        });
    }
};