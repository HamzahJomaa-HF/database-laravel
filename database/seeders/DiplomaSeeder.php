<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DiplomaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diplomas = [
            'None',
            'Primary School', 
            'Intermediate School',
            'Secondary School',
            'High School',
            'Associate Degree',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'Doctorate/PhD',
            'Vocational Training',
            'Technical Diploma',
            'Other'
        ];

        $now = now();
        
        foreach ($diplomas as $diplomaName) {
            DB::table('diploma')->insert([
                'diploma_id' => Str::uuid(),
                'diploma_name' => $diplomaName,
                'external_id' => 'DIP_' . Str::upper(Str::random(8)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $this->command->info("✅ Added diploma: {$diplomaName}");
        }
        
        $this->command->info("\n🎓 Diplomas seeding completed! Total: " . count($diplomas));
    }
}