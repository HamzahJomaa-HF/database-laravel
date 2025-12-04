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
            // First, drop the old phone_number column if it exists
            if (Schema::hasColumn('users', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            
                        // First, drop the old phone_number column if it exists
            if (Schema::hasColumn('users', 'mobile_phone')) {
                            // Then rename mobile_phone to phone_number
            $table->renameColumn('mobile_phone', 'phone_number');
            }
            

            
            // Make phone_number non-nullable since it's required
            $table->string('phone_number')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                        // First, drop the old phone_number column if it exists
            if (Schema::hasColumn('users', 'mobile_phone')) {
                            // Then rename mobile_phone to phone_number
            $table->renameColumn('mobile_phone', 'phone_number');
            }
            
            // Then re-add the old phone_number column
            $table->string('phone_number')->nullable()->after('mobile_phone');
        });
    }
};