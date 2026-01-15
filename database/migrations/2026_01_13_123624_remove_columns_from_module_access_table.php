<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('module_access', function (Blueprint $table) {
            // Drop the three columns
            $table->dropColumn(['employee_id', 'resource_id', 'resource_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('module_access', function (Blueprint $table) {
            // Simply re-add the columns - you can add foreign key separately if needed
            $table->uuid('employee_id')->nullable();
            $table->string('resource_id')->nullable();
            $table->string('resource_type')->nullable();
        });
    }
};