<?php

return [
    'currency' => env('CURRENCY', 'OMR'),

    'commission' => [
        'type' => env('COMMISSION_TYPE', 'percentage'),
        'value' => env('COMMISSION_VALUE', 10),
    ],

    'booking' => [
        'slots' => [
            'morning' => ['start' => '08:00', 'end' => '14:00'],
            'evening' => ['start' => '16:00', 'end' => '22:00'],
        ],
        'advance_days' => 90,
        'min_advance_hours' => 24,
    ],

    'sms' => [
        'driver' => env('SMS_DRIVER', 'log'),
        'api_key' => env('SMS_API_KEY'),
        'api_url' => env('SMS_API_URL'),
    ],

    'maps' => [
        'provider' => env('GOOGLE_MAPS_API_KEY') ? 'google' : 'leaflet',
        'google_api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],
];
