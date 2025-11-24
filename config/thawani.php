<?php

return [
    'api_key' => env('THAWANI_API_KEY'),
    // 'secret_key' => env('THAWANI_SECRET_KEY'),
    // 'mode' => env('THAWANI_MODE', 'sandbox'),
    // 'return_url' => env('THAWANI_RETURN_URL'),
    // 'cancel_url' => env('THAWANI_CANCEL_URL'),
    // 'webhook_secret' => env('THAWANI_WEBHOOK_SECRET'),

    // Thawani API endpoints
    'base_url' => env('THAWANI_BASE_URL', 'https://uatcheckout.thawani.om/api/v1'),
    'secret_key' => env('THAWANI_SECRET_KEY'),
    'publishable_key' => env('THAWANI_PUBLISHABLE_KEY'),
];
