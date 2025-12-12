<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            $table->dropColumn([
                'focalpoint_code',
                'position',
                'department',
                'email',
                'phone',
                'responsibility_level',
                'is_active'
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('rp_focalpoints', function (Blueprint $table) {
            $table->string('focalpoint_code', 255)->nullable();
            $table->string('position', 255)->nullable();
            $table->string('department', 255)->nullable();
            $table->string('email', 255)->nullable();
            $table->string('phone', 255)->nullable();
            $table->string('responsibility_level', 255)->nullable();
            $table->boolean('is_active')->default(true);
        });
    }
};