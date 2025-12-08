// database/migrations/xxxx_add_first_last_name_to_system_users_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('system_users', function (Blueprint $table) {
            $table->string('first_name')->after('id')->nullable();
            $table->string('last_name')->after('first_name')->nullable();
        });
    }

    public function down()
    {
        Schema::table('system_users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name']);
        });
    }
};