<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('module_access', function (Blueprint $table) {
            $table->string('description', 500)->nullable()->after('access_level');
        });
    }

    public function down(): void
    {
        Schema::table('module_access', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};