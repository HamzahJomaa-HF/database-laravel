// database/migrations/xxxx_add_auth_columns_to_employees_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Add authentication columns if they don't exist
            if (!Schema::hasColumn('employees', 'password')) {
                $table->string('password')->nullable()->after('email');
            }
            
            if (!Schema::hasColumn('employees', 'remember_token')) {
                $table->rememberToken()->after('password');
            }
            
            if (!Schema::hasColumn('employees', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('remember_token');
            }
            
            if (!Schema::hasColumn('employees', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('email_verified_at');
            }
            
            // Ensure email is unique for authentication
            $table->string('email')->unique()->change();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'email_verified_at', 'is_active']);
            $table->string('email')->unique(false)->change();
        });
    }
};