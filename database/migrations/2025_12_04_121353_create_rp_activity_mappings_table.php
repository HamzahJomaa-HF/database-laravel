<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_activity_mappings', function (Blueprint $table) {
            // Primary Key
            $table->uuid('rp_activity_mappings_id')->primary();
            
            // Reporting Activity Reference
            $table->uuid('rp_activity_id');
            
            // Dual Reference System (for maximum flexibility)
            // Option 1: Reference by internal UUID (when you have database access)
            $table->uuid('main_activity_id')->nullable()->comment('Internal UUID reference to activities.activity_id');
            
            // Option 2: Reference by external string ID (when dealing with external systems)
            $table->string('external_activity_id')->nullable()->comment('External system ID (string reference)');
            $table->string('external_activity_code')->nullable()->comment('External system code for easier lookup');
            
            // System Information
            $table->string('external_type')->default('activities')->comment('Type of external system: "activities", "erp", "crm", etc.');
            $table->string('external_system_name')->nullable()->comment('Name of the external system');
            
            // Mapping Configuration
            $table->enum('mapping_type', ['direct', 'aggregated', 'partial', 'hierarchical', 'custom'])->default('direct');
            $table->decimal('mapping_percentage', 5, 2)->nullable()->comment('Percentage mapping (0-100) if partial mapping');
            $table->enum('sync_direction', ['one_way', 'two_way', 'read_only', 'write_only'])->default('one_way');
            
            // Mapping Metadata
            $table->text('mapping_notes')->nullable()->comment('Notes about this mapping');
            $table->text('sync_rules')->nullable()->comment('JSON rules for data synchronization');
            $table->date('mapped_date')->nullable()->comment('Date when mapping was established');
            $table->date('valid_from')->nullable()->comment('Mapping valid from date');
            $table->date('valid_to')->nullable()->comment('Mapping valid until date');
            
            // Sync Status
            $table->boolean('is_active')->default(true);
            $table->boolean('sync_pending')->default(false)->comment('Flag if sync is pending');
            $table->string('sync_status')->default('pending')->comment('Current sync status');
            $table->timestamp('last_sync_at')->nullable()->comment('Last successful sync timestamp');
            $table->text('last_sync_result')->nullable()->comment('Result of last sync attempt');
            
            // Timestamps
            $table->timestamps();
            
           
            
            // Indexes for Performance (keep these)
            $table->index('rp_activity_id');
            $table->index('main_activity_id');
            $table->index('external_activity_id');
            $table->index('external_activity_code');
            $table->index('external_type');
            $table->index('mapping_type');
            $table->index('sync_direction');
            $table->index('is_active');
            $table->index('sync_status');
            $table->index('sync_pending');
            $table->index(['valid_from', 'valid_to']);
            
            // Unique Constraints (Allow multiple mapping types but prevent exact duplicates)
            $table->unique(['rp_activity_id', 'main_activity_id', 'external_type'], 'unique_internal_mapping');
            $table->unique(['rp_activity_id', 'external_activity_id', 'external_type'], 'unique_external_mapping');
            
            // Partial Index for Active Mappings
            $table->index(['rp_activity_id', 'is_active']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_activity_mappings');
    }
};