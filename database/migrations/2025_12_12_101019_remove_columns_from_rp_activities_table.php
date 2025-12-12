<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_activities', function (Blueprint $table) {
            $table->dropColumn([
                'activity_title_en',
                'activity_title_ar',
                'folder_name',
                'content_network',
                'reporting_period_start',
                'reporting_period_end',
                'achievements',
                'challenges',
                'next_steps',
                'allocated_budget',
                'spent_budget',
                'is_active',
                'needs_sync',
                'last_sync_at'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rp_activities', function (Blueprint $table) {
            $table->string('activity_title_en', 255)->nullable();
            $table->string('activity_title_ar', 255)->nullable();
            $table->string('folder_name', 255)->nullable();
            $table->text('content_network')->nullable();
            $table->date('reporting_period_start')->nullable();
            $table->date('reporting_period_end')->nullable();
            $table->text('achievements')->nullable();
            $table->text('challenges')->nullable();
            $table->text('next_steps')->nullable();
            $table->decimal('allocated_budget', 10, 2)->nullable();
            $table->decimal('spent_budget', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('needs_sync')->default(false);
            $table->timestamp('last_sync_at')->nullable();
        });
    }
};