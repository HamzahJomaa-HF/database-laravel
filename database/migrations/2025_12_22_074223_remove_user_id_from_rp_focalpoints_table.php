<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            // First, check if there are any records with user_id
            $hasUserIds = DB::table('rp_focalpoints')
                ->whereNotNull('user_id')
                ->exists();
            
            if ($hasUserIds) {
                // Log warning but continue
                \Log::warning('rp_focalpoints table contains user_id data that will be removed.');
            }
            
            // Remove the user_id column
            $table->dropColumn('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            // Add user_id column back (nullable)
            $table->uuid('user_id')->nullable()->after('name');
        });
    }
};