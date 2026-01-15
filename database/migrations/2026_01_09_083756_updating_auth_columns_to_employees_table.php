<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Instead of adding, we'll remove the authentication columns
            if (Schema::hasColumn('employees', 'password')) {
                $table->dropColumn('password');
            }
            
            if (Schema::hasColumn('employees', 'remember_token')) {
                $table->dropColumn('remember_token');
            }
            
            // Note: We're keeping email_verified_at and is_active since they're in your sample data
            // and you only asked to remove password and remember_token
            
            // Keep the email unique constraint if you want it
            // $table->string('email')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Re-add the columns if rolling back
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
        });
    }
};