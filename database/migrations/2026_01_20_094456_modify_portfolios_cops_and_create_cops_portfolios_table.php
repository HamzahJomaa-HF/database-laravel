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
        // 1. Drop start_date and end_date from portfolios table
        Schema::table('portfolios', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });

        // 2. Drop program_id from cops table
        Schema::table('cops', function (Blueprint $table) {
            $table->dropForeign(['program_id']);
            $table->dropColumn('program_id');
        });

        // 3. Create cops_portfolios junction table for many-to-many relationship
        Schema::create('cops_portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cop_id');
            $table->uuid('portfolio_id');
           
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();

            // Foreign key constraints
            $table->foreign('cop_id')
                  ->references('cop_id')
                  ->on('cops')
                  ->onDelete('cascade');
                  
            $table->foreign('portfolio_id')
                  ->references('portfolio_id')
                  ->on('portfolios')
                  ->onDelete('cascade');

            // Add unique constraint to prevent duplicate relationships
            $table->unique(['cop_id', 'portfolio_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 3. Drop cops_portfolios junction table
        Schema::dropIfExists('cops_portfolios');

        // 2. Add back program_id to cops table
        Schema::table('cops', function (Blueprint $table) {
            $table->uuid('program_id')->nullable()->after('external_id');
            $table->foreign('program_id')
                  ->references('program_id')
                  ->on('programs')
                  ->onDelete('set null');
        });

        // 1. Add back start_date and end_date to portfolios table
        Schema::table('portfolios', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->date('end_date')->nullable()->after('start_date');
        });
    }
};