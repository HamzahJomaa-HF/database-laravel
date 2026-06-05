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
        Schema::create('activity_financials', function (Blueprint $table) {
            // Primary Key
            $table->uuid('activity_financial_id')->primary();
            
            // Foreign Key Columns
            $table->uuid('activity_id');
            $table->uuid('user_id');
            $table->uuid('cop_id')->nullable();
            
            // Financial Fields
            $table->string('financial_type', 50); // omt, medical, education
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('payment_status', 50)->nullable(); // pending, partial, paid, overdue
            $table->date('tx_date')->nullable(); // Transaction Date
            
            // Flexible Data & Metadata
            $table->jsonb('financial_data')->default(json_encode([]));
            $table->uuid('external_id')->nullable();
            $table->text('notes')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes (for performance)
            $table->index('activity_id');
            $table->index('user_id');
            $table->index('cop_id');
            $table->index('financial_type');
            $table->index('payment_status');
            $table->index('tx_date');
            $table->index('external_id');
            $table->index('deleted_at');
            
            // =============================================
            // IMPORTANT: NO UNIQUE CONSTRAINT ON activity_id
            // This allows multiple financial records per activity
            // (one per user)
            // =============================================
            // $table->unique('activity_id'); // ← COMMENTED OUT - DO NOT UNCOMMENT
            
            // Composite unique constraint (optional - prevents duplicate user+activity)
            // $table->unique(['activity_id', 'user_id']); // ← Uncomment if you want one record per user per activity
            
            // Foreign Key Constraints
            $table->foreign('activity_id')
                  ->references('activity_id')
                  ->on('activities')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('user_id')
                  ->references('user_id')
                  ->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
                  
            $table->foreign('cop_id')
                  ->references('cop_id')
                  ->on('cops')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_financials');
    }
};