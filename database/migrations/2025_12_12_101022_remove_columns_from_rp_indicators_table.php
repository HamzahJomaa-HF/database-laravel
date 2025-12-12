<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_indicators', function (Blueprint $table) {
            $table->dropColumn([
                'indicator_code',
                'unit_of_measure',
                'baseline_value',
                'baseline_date',
                'target_date',
                'frequency',
                'calculation_method',
                'is_active'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rp_indicators', function (Blueprint $table) {
            $table->string('indicator_code', 255)->nullable();
            $table->string('unit_of_measure', 255)->nullable();
            $table->decimal('baseline_value', 10, 2)->nullable();
            $table->date('baseline_date')->nullable();
            $table->date('target_date')->nullable();
            $table->string('frequency', 255)->nullable();
            $table->text('calculation_method')->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};