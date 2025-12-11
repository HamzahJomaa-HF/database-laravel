<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            if (!Schema::hasColumn('rp_focalpoints', 'type')) {
                $table->enum('type', ['main_activity', 'rp_activity', 'general'])
                      ->default('rp_activity')
                      ->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            $table->dropColumnIfExists('type');
        });
    }
};