<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_activities', function (Blueprint $table) {
            // Primary Key
            $table->uuid('rp_activities_id')->primary();
            
            // Hierarchy Foreign Key
            $table->uuid('action_id');
            
            // External System References
            $table->uuid('external_id')->nullable()->comment('UUID from external activities system');
            $table->string('external_type')->nullable()->comment('Source system, e.g., "main_activities", "external_system"');
            
            // Basic Activity Information
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            
            // Fields to Sync with Main Activities Table
            $table->string('activity_title_en')->nullable()->comment('English title synced from main activities');
            $table->string('activity_title_ar')->nullable()->comment('Arabic title synced from main activities');
            $table->string('folder_name')->nullable()->comment('Folder name synced from main activities');
            $table->text('content_network')->nullable()->comment('Content network synced from main activities');
            
            // Activity Metadata
            $table->string('activity_type')->nullable()->comment('Type/category of activity');
            $table->date('reporting_period_start')->nullable()->comment('Start date for reporting period');
            $table->date('reporting_period_end')->nullable()->comment('End date for reporting period');
            
            // Status and Progress
            $table->enum('status', ['planned', 'ongoing', 'completed', 'cancelled', 'on_hold'])->default('ongoing');
            $table->text('achievements')->nullable()->comment('Accomplishments in this period');
            $table->text('challenges')->nullable()->comment('Challenges faced');
            $table->text('next_steps')->nullable()->comment('Planned next steps');
            
            // Budget and Resources
            $table->decimal('allocated_budget', 15, 2)->nullable()->comment('Budget allocated for this activity');
            $table->decimal('spent_budget', 15, 2)->nullable()->comment('Budget spent so far');
            
            // Timestamps and Flags
            $table->boolean('is_active')->default(true);
            $table->boolean('needs_sync')->default(false)->comment('Flag if data needs sync with main activities');
            $table->timestamp('last_sync_at')->nullable()->comment('Last sync timestamp with main activities');
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('action_id')
                  ->references('rp_actions_id')
                  ->on('rp_actions')
                  ->onDelete('cascade');
            
            // Indexes for Performance
            $table->index(['external_id', 'external_type']);
            $table->index('status');
            $table->index('is_active');
            $table->index('activity_type');
            $table->index('needs_sync');
            $table->index('reporting_period_start');
            $table->index('reporting_period_end');
            
            // Compound Indexes for Common Queries
            $table->index(['status', 'is_active']);
            $table->index(['reporting_period_start', 'reporting_period_end']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_activities');
    }
};