<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('rp_programs', function (Blueprint $table) {
            $table->uuid('rp_programs_id')->primary();
            $table->uuid('component_id');
            $table->uuid('external_id')->nullable()->comment('ID from external system');
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 15, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('rp_programs');
    }
};