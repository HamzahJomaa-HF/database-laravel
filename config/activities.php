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
    | Operational support recipients
    |--------------------------------------------------------------------------
    |
    | Each operational support checkbox emails its own recipient(s) when it is
    | selected on an activity. Comma-separated per key, set in .env.
    |
    */

    'operational_support_emails' => [
        'logistics' => env('SUPPORT_EMAIL_LOGISTICS', ''),
        'data' => env('SUPPORT_EMAIL_DATA', ''),
        'public_relations' => env('SUPPORT_EMAIL_PUBLIC_RELATIONS', ''),
        'media' => env('SUPPORT_EMAIL_MEDIA', ''),
        'field_support' => env('SUPPORT_EMAIL_FIELD_SUPPORT', ''),
    ],

];
