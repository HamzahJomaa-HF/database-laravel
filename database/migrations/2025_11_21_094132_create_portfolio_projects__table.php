<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('project_portfolios', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('project_id');
            $table->uuid('portfolio_id');
            $table->integer('order')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Add actual foreign key constraints (not just in foreign_keys table)
            $table->foreign('project_id', 'FK_PROJECT_PORTFOLIOS_PROJECT_ID')
                  ->references('project_id')
                  ->on('projects')
                  ->onDelete('cascade');
                  
            $table->foreign('portfolio_id', 'FK_PROJECT_PORTFOLIOS_PORTFOLIO_ID')
                  ->references('portfolio_id')
                  ->on('portfolios')
                  ->onDelete('cascade');
            
            $table->unique(['project_id', 'portfolio_id']);
        });

        // REMOVE THIS - no need for foreign_keys table inserts
        // DB::table('foreign_keys')->insert([...]);
    }

    public function down(): void
    {
        Schema::dropIfExists('project_portfolios');
        
        // REMOVE THIS TOO
        // DB::table('foreign_keys')->where('table_name', 'project_portfolios')->delete();
    }
};