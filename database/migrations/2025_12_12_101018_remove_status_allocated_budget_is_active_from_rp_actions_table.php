<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->dropColumn(['status', 'allocated_budget', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->string('status', 255)->nullable();
            $table->decimal('allocated_budget', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};