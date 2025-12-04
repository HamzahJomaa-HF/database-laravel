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
        Schema::table('users', function (Blueprint $table) {
            // Add the default_cop_id column if it doesn't exist
            if (!Schema::hasColumn('users', 'default_cop_id')) {
                // Use foreignUuid() since cop_id is a UUID
                $table->foreignUuid('default_cop_id')
                    ->nullable()
                    ->constrained('cops', 'cop_id')
                    ->onDelete('set null');
            }
            
            // If you have an old community_of_practice column, you might want to remove it
            if (Schema::hasColumn('users', 'community_of_practice')) {
                $table->dropColumn('community_of_practice');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['default_cop_id']);
            $table->dropColumn('default_cop_id');
            
            // If you want to restore the old column
            $table->string('community_of_practice')->nullable();
        });
    }
};
