<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            if (!Schema::hasColumn('rp_focalpoints', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('rp_focalpoints_id');
                
                $table->foreign('user_id', 'FK_RP_FOCALPOINTS_USER_ID')
                      ->references('user_id')->on('users')
                      ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            $table->dropForeignIfExists('FK_RP_FOCALPOINTS_USER_ID');
            $table->dropColumnIfExists('user_id');
        });
    }
};