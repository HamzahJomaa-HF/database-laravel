<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixRemainingUniqueConstraints extends Migration
{
    public function up()
    {
        // Fix rp_units - change from code-only to (code + program_id)
        DB::statement('ALTER TABLE rp_units DROP CONSTRAINT IF EXISTS rp_units_code_unique;');
        DB::statement('ALTER TABLE rp_units ADD CONSTRAINT rp_units_code_program_id_unique UNIQUE (code, program_id);');
        
        // Fix rp_programs - change from code-only to (code + component_id)
        DB::statement('ALTER TABLE rp_programs DROP CONSTRAINT IF EXISTS rp_programs_code_unique;');
        DB::statement('ALTER TABLE rp_programs ADD CONSTRAINT rp_programs_code_component_id_unique UNIQUE (code, component_id);');
        
        // rp_components and rp_actions are already correct
    }

    public function down()
    {
        DB::statement('ALTER TABLE rp_units DROP CONSTRAINT IF EXISTS rp_units_code_program_id_unique;');
        DB::statement('ALTER TABLE rp_units ADD CONSTRAINT rp_units_code_unique UNIQUE (code);');
        
        DB::statement('ALTER TABLE rp_programs DROP CONSTRAINT IF EXISTS rp_programs_code_component_id_unique;');
        DB::statement('ALTER TABLE rp_programs ADD CONSTRAINT rp_programs_code_unique UNIQUE (code);');
    }
}