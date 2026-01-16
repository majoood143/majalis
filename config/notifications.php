<?php

declare(strict_types=1);

/**
 * Notification Configuration
 * 
 * Configure notification channels, templates, and behavior for the Majalis platform.
 * 
 * @package config
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Channels
    |--------------------------------------------------------------------------
    |
    | Configure each notification channel with its settings.
    | Channels can be enabled/disabled independently.
    |
    */
    'channels' => [
        /*
        |--------------------------------------------------------------------------
        | Email Channel
        |--------------------------------------------------------------------------
        */
        'email' => [
            'enabled' => env('NOTIFICATION_EMAIL_ENABLED', true),
            'queue' => env('NOTIFICATION_EMAIL_QUEUE', 'notifications'),
            'retry_times' => env('NOTIFICATION_EMAIL_RETRY', 3),
            'retry_delay' => env('NOTIFICATION_EMAIL_RETRY_DELAY', 300), // 5 minutes
            'from_address' => env('MAIL_FROM_ADDRESS', 'noreply@majalis.om'),
            'from_name' => env('MAIL_FROM_NAME', 'Majalis'),
        ],

        /*
        |--------------------------------------------------------------------------
        | SMS Channel (Future Implementation)
        |--------------------------------------------------------------------------
        */
        'sms' => [
            'enabled' => env('NOTIFICATION_SMS_ENABLED', false),
            'queue' => env('NOTIFICATION_SMS_QUEUE', 'sms'),
            'retry_times' => env('NOTIFICATION_SMS_RETRY', 3),
            'retry_delay' => env('NOTIFICATION_SMS_RETRY_DELAY', 60), // 1 minute
            'provider' => env('SMS_PROVIDER', 'twilio'), // twilio, unifonic, ooredoo
            'default_country_code' => env('SMS_DEFAULT_COUNTRY_CODE', '+968'),
            
            // Provider-specific settings
            'providers' => [
                'twilio' => [
                    'sid' => env('TWILIO_SID'),
                    'token' => env('TWILIO_TOKEN'),
                    'from' => env('TWILIO_FROM'),
                ],
                'unifonic' => [
                    'app_sid' => env('UNIFONIC_APP_SID'),
                    'sender_id' => env('UNIFONIC_SENDER_ID', 'Majalis'),
                ],
                'ooredoo' => [
                    'username' => env('OOREDOO_SMS_USERNAME'),
                    'password' => env('OOREDOO_SMS_PASSWORD'),
                    'sender_id' => env('OOREDOO_SENDER_ID', 'Majalis'),
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Push Notification Channel (Future Implementation)
        |--------------------------------------------------------------------------
        */
        'push' => [
            'enabled' => env('NOTIFICATION_PUSH_ENABLED', false),
            'queue' => env('NOTIFICATION_PUSH_QUEUE', 'notifications'),
            'provider' => env('PUSH_PROVIDER', 'fcm'), // fcm, onesignal
            
            'providers' => [
                'fcm' => [
                    'server_key' => env('FCM_SERVER_KEY'),
                    'project_id' => env('FCM_PROJECT_ID'),
                ],
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | In-App Notifications
        |--------------------------------------------------------------------------
        */
        'in_app' => [
            'enabled' => env('NOTIFICATION_INAPP_ENABLED', true),
        ],

        /*
        |--------------------------------------------------------------------------
        | WhatsApp Channel (Future Implementation)
        |--------------------------------------------------------------------------
        */
        'whatsapp' => [
            'enabled' => env('NOTIFICATION_WHATSAPP_ENABLED', false),
            'queue' => env('NOTIFICATION_WHATSAPP_QUEUE', 'whatsapp'),
            'provider' => env('WHATSAPP_PROVIDER', 'twilio'), // twilio, 360dialog
            
            'providers' => [
                'twilio' => [
                    'sid' => env('TWILIO_SID'),
                    'token' => env('TWILIO_TOKEN'),
                    'from' => env('TWILIO_WHATSAPP_FROM'),
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Settings
    |--------------------------------------------------------------------------
    |
    | Configure template rendering behavior.
    |
    */
    'templates' => [
        'default_locale' => env('NOTIFICATION_DEFAULT_LOCALE', 'en'),
        'fallback_locale' => env('NOTIFICATION_FALLBACK_LOCALE', 'ar'),
        'email_path' => 'emails.booking',
    ],

    /*
    |--------------------------------------------------------------------------
    | Queue Settings
    |--------------------------------------------------------------------------
    |
    | Default queue settings for notifications.
    |
    */
    'queue' => [
        'connection' => env('NOTIFICATION_QUEUE_CONNECTION', 'database'),
        'default_queue' => env('NOTIFICATION_DEFAULT_QUEUE', 'notifications'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Configure retry behavior for failed notifications.
    |
    */
    'retry' => [
        'max_attempts' => env('NOTIFICATION_MAX_RETRY', 3),
        'backoff' => [60, 300, 900], // 1min, 5min, 15min
        'auto_retry_enabled' => env('NOTIFICATION_AUTO_RETRY', true),
        'retry_schedule' => '*/5 * * * *', // Every 5 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    |
    | Configure notification logging behavior.
    |
    */
    'logging' => [
        'enabled' => env('NOTIFICATION_LOGGING_ENABLED', true),
        'channel' => env('NOTIFICATION_LOG_CHANNEL', 'stack'),
        'log_success' => env('NOTIFICATION_LOG_SUCCESS', true),
        'log_failure' => env('NOTIFICATION_LOG_FAILURE', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Prevent notification spam.
    |
    */
    'rate_limiting' => [
        'enabled' => env('NOTIFICATION_RATE_LIMIT_ENABLED', true),
        'max_per_user_per_hour' => env('NOTIFICATION_MAX_PER_USER_HOUR', 20),
        'max_per_booking_per_hour' => env('NOTIFICATION_MAX_PER_BOOKING_HOUR', 5),
    ],
];
