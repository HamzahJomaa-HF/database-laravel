<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Always-notify emails
    |--------------------------------------------------------------------------
    |
    | Comma-separated list of email addresses that get notified every time a
    | new activity is created, regardless of which focal points were
    | selected on the form. Set via ACTIVITY_ALWAYS_NOTIFY_EMAILS in .env.
    |
    */

    'always_notify_emails' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('ACTIVITY_ALWAYS_NOTIFY_EMAILS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Detailed operational support recipients
    |--------------------------------------------------------------------------
    |
    | Ticking any option in a group (config/support_options.php) emails that
    | group's recipients. The options under 'support_special_emails' also email
    | their own dedicated recipient(s). Comma-separated, set in .env.
    |
    */

    'support_group_emails' => [
        'dmesc' => env('SUPPORT_EMAIL_DMESC', ''),
        'logproc' => env('SUPPORT_EMAIL_LOGPROC', ''),
        'comms' => env('SUPPORT_EMAIL_COMMS', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | President involved recipients
    |--------------------------------------------------------------------------
    |
    | Emailed when "President Involved" is ticked on an activity.
    | Comma-separated, set in .env.
    |
    */

    'president_involved_emails' => env('PRESIDENT_INVOLVED_EMAILS', ''),

    /*
    |--------------------------------------------------------------------------
    | Venue recipients
    |--------------------------------------------------------------------------
    |
    | When an activity's venue contains the 'match' text, the matching
    | recipient(s) are emailed (first match wins). Comma-separated, set in .env.
    | Venues not listed here send no email.
    |
    */

    'venue_emails' => [
        ['match' => 'HF Beirut Office', 'emails' => env('VENUE_EMAIL_BEIRUT_OFFICE', '')],
        ['match' => 'National State Academy', 'emails' => env('VENUE_EMAIL_NATIONAL_STATE_ACADEMY', '')],
        ['match' => 'Khan El Franj', 'emails' => env('VENUE_EMAIL_KHAN_EL_FRANJ', '')],
        ['match' => 'Outreach & Leadership', 'emails' => env('VENUE_EMAIL_OUTREACH_LEADERSHIP', '')],
        ['match' => 'Taamir Ein El Hilweh', 'emails' => env('VENUE_EMAIL_TAAMIR_EIN_EL_HILWEH', '')],
        ['match' => 'Vocational & Technical Training Center', 'emails' => env('VENUE_EMAIL_VOCATIONAL_TRAINING', '')],
        ['match' => 'Cisco Academy', 'emails' => env('VENUE_EMAIL_CISCO_ACADEMY', '')],
        ['match' => 'Hariri Social & Medical Center', 'emails' => env('VENUE_EMAIL_SOCIAL_MEDICAL_CENTER', '')],
    ],

    'support_special_emails' => [
        'logproc__live_streaming' => env('SUPPORT_EMAIL_TECHNICAL', ''),
        'logproc__hardware_needed' => env('SUPPORT_EMAIL_TECHNICAL', ''),
        'logproc__wifi_access' => env('SUPPORT_EMAIL_TECHNICAL', ''),
    ],

];
