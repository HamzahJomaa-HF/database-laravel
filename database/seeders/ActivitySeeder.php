<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activityTypes = ['workshop', 'campaign', 'training', 'conference', 'seminar'];
        $venues = [
            'Rafic Hariri High School - Main Hall',
            'Hariri Social & Medical Center',
            'Cisco Academy - Vocational Training Center',
            'Anamilouna - Women Empowerment Center',
            'Community Outreach & Support Office',
        ];

        for ($i = 1; $i <= 50; $i++) {
            $type = $activityTypes[($i - 1) % count($activityTypes)];
            $venue = $venues[($i - 1) % count($venues)];
            $startDate = Carbon::now()->addDays(rand(-180, 180));
            $endDate = (clone $startDate)->addDays(rand(1, 5));

            Activity::create([
                'external_id' => 'ACT_SEED_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'folder_name' => 'ACT' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'activity_title_en' => ucfirst($type) . ' Activity ' . $i,
                'activity_title_ar' => 'نشاط رقم ' . $i,
                'activity_type' => $type,
                'content_network' => 'Seeded activity for testing purposes #' . $i,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'venue' => $venue,
                'maximum_capacity' => rand(20, 100),
            ]);
        }

        $this->command->info('✅ Successfully seeded 50 activities!');
    }
}
