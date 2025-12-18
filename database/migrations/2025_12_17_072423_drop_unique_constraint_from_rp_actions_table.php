<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DropUniqueConstraintFromRpActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop the existing unique constraint
            DB::statement('ALTER TABLE rp_actions DROP CONSTRAINT IF EXISTS rp_actions_code_unique;');
            
            // Add composite unique constraint (code + unit_id)
            DB::statement('ALTER TABLE rp_actions ADD CONSTRAINT rp_actions_code_unit_id_unique UNIQUE (code, unit_id);');
        } 
        // For MySQL
        elseif (DB::connection()->getDriverName() === 'mysql') {
            Schema::table('rp_actions', function (Blueprint $table) {
                // Drop the existing unique index
                $table->dropUnique('rp_actions_code_unique');
                
                // Add composite unique constraint
                $table->unique(['code', 'unit_id'], 'rp_actions_code_unit_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (DB::connection()->getDriverName() === 'pgsql') {
            // Drop the composite unique constraint
            DB::statement('ALTER TABLE rp_actions DROP CONSTRAINT IF EXISTS rp_actions_code_unit_id_unique;');
            
            // Restore the original unique constraint on code only
            DB::statement('ALTER TABLE rp_actions ADD CONSTRAINT rp_actions_code_unique UNIQUE (code);');
        } 
        elseif (DB::connection()->getDriverName() === 'mysql') {
            Schema::table('rp_actions', function (Blueprint $table) {
                // Drop the composite unique index
                $table->dropUnique('rp_actions_code_unit_id_unique');
                
                // Restore the original unique index on code only
                $table->unique('code', 'rp_actions_code_unique');
            });
        }
    }
}