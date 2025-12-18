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
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->renameColumn('description', 'objectives');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rp_actions', function (Blueprint $table) {
            $table->renameColumn('objectives', 'description');
        });
    }
};