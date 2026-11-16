<?php

// Detailed operational support options, grouped. Stored in activities.operational_support
// under "<group>__<option>" keys.
return [
    'dmesc' => [
        'title' => '1. Data, Monitoring & Evaluation & Stakeholder-Communications',
        'options' => [
            'invitations' => 'Invitations (select all that applies)',
            'attendance_sheet' => 'Attendance Sheet (select all that applies)',
            'minutes_of_meeting' => 'Minutes of Meeting (select all that applies)',
            'registration_form' => 'Registration Form',
            'post_event_evaluation' => 'Post-Event Evaluation/Feedback Forms',
            'participation_certificate' => 'Participation Certificate (select all that applies)',
        ],
    ],
    'logproc' => [
        'title' => '2. Logistics & Procurement',
        'options' => [
            'sound_system' => 'Sound System (select all that applies)',
            'live_streaming' => 'Live Streaming (Zoom Call)',
            'setup' => 'Setup (select all that applies)',
            'setup_type' => 'Setup Type',
            'screen_projection' => 'Screen Projection (select all that applies)',
            'hardware_needed' => 'Hardware Needed',
            'wifi_access' => 'WiFi Access',
            'rollups_needed' => 'Rollups Needed',
            'flags_needed' => 'Flags Needed',
            'podium' => 'Podium',
            'food_beverages' => 'Food & Beverages (select all that applies)',
            'note_papers_pens' => 'Note Papers and Pens',
            'hf_staff_assistance' => "Names of HF Staff's Assistance Required",
            'hf_volunteers_assistance' => "Number of HF Volunteer's Assistance Required",
        ],
    ],
    'comms' => [
        'title' => 'Communications',
    'note' => 'Please note: the Communications Department will review your request and assess the services and needs accordingly, based on the requirements of the event.',
        'options' => [
            'photography' => 'Photography',
            'videography' => 'Videography',
            'social_media_during' => 'Social Media Coverage (During Event)',
            'social_media_post' => 'Post-Event Social Media Content',
            'website_news' => 'Website / News Article',
            'graphic_design' => 'Graphic Design / Visual Materials',
            'other' => 'Other (please specify):',
        ],
    ],
];
