<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSessionsTableForUuid extends Migration
{
    public function up()
    {
        Schema::table('sessions', function (Blueprint $table) {
            // Change user_id to string to store UUIDs
            $table->string('user_id', 36)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('sessions', function (Blueprint $table) {
            $table->bigInteger('user_id')->nullable()->change();
        });
    }
}