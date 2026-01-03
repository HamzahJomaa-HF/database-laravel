<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        // // Activity 1
        // DB::table('activities')->insert([
        //     'activity_id' => Str::uuid(),
        //     'external_id' => 'ACT_2025_12_001',
        //     'folder_name' => 'ACT001',
        //     'activity_title_en' => 'Teachers Training Workshop',
        //     'activity_title_ar' => 'ورشة تدريب المعلمين',
        //     'activity_type' => 'workshop',
        //     'content_network' => 'Training for RHHS teachers on modern teaching methodologies',
        //     'start_date' => '2025-03-15',
        //     'end_date' => '2025-03-17',
        //     'venue' => 'Rafic Hariri High School - Main Hall',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Activity 2
        // DB::table('activities')->insert([
        //     'activity_id' => Str::uuid(),
        //     'external_id' => 'ACT_2025_12_002',
        //     'folder_name' => 'ACT002',
        //     'activity_title_en' => 'Health Awareness Campaign',
        //     'activity_title_ar' => 'حملة التوعية الصحية',
        //     'activity_type' => 'campaign',
        //     'content_network' => 'Community health awareness about diabetes and hypertension',
        //     'start_date' => '2025-04-10',
        //     'end_date' => '2025-04-12',
        //     'venue' => 'Hariri Social & Medical Center',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);

        // // Activity 3
        // DB::table('activities')->insert([
        //     'activity_id' => Str::uuid(),
        //     'external_id' => 'ACT_2025_12_003',
        //     'folder_name' => 'ACT003',
        //     'activity_title_en' => 'Digital Skills Training for Youth',
        //     'activity_title_ar' => 'تدريب المهارات الرقمية للشباب',
        //     'activity_type' => 'training',
        //     'content_network' => 'Basic digital literacy and computer skills for unemployed youth',
        //     'start_date' => '2025-05-05',
        //     'end_date' => '2025-05-09',
        //     'venue' => 'Cisco Academy - Vocational Training Center',
        //     'created_at' => now(),
        //     'updated_at' => now(),
        // ]);
    }
}