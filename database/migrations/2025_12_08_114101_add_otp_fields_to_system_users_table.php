<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Migration content
public function up()
{
    Schema::table('system_users', function (Blueprint $table) {
        $table->string('temp_otp', 6)->nullable();
        $table->timestamp('otp_expires_at')->nullable();
        $table->string('otp_session_token', 32)->nullable();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('system_users', function (Blueprint $table) {
            //
        });
    }
};
