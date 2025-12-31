<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NationalitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nationalities = [
            'Lebanese',
            'Syrian',
            'Palestinian', 
            'Egyptian',
            'Jordanian',
            'Iraqi',
            'Saudi',
            'Emirati',
            'Qatari',
            'Kuwaiti',
            'American',
            'Canadian',
            'British',
            'French',
            'German',
            'Other'
        ];

        $now = now();
        
        foreach ($nationalities as $nationalityName) {
            DB::table('nationality')->insert([
                'nationality_id' => Str::uuid(),
                'name' => $nationalityName,
                'external_id' => 'NAT_' . Str::upper(Str::random(8)),
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $this->command->info("✅ Added nationality: {$nationalityName}");
        }
        
        $this->command->info("\n🌍 Nationalities seeding completed! Total: " . count($nationalities));
    }
}