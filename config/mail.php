<?php

/**
 * Mail Configuration
 *
 * This configuration file controls how emails are sent from your application.
 *
 * Key settings:
 * - default: The default mailer to use (set via MAIL_MAILER environment variable)
 * - mailers: Array of available mail service configurations
 * - from: Global from address used for all emails
 *
 * For Majalis (Oman hall booking platform):
 * - Use 'smtp' for production with Oman-based mail server
 * - Use 'log' for local development (emails saved to logs)
 *
 * @package config
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | This option controls the default mailer that is used to send all email
    | messages unless another mailer is explicitly specified when sending
    | the message. All additional mailers can be configured within the
    | "mailers" array below. Examples of each type of mailer are provided.
    |
    | Supported: "smtp", "sendmail", "mailgun", "postmark", "ses", "resend",
    |            "log", "array", "failover", "roundrobin"
    |
    | Development: Use 'log' to capture emails in storage/logs/laravel.log
    | Production: Use 'smtp' with proper MAIL_MAILER environment variable
    |
    */

    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the mailers used by your application plus
    | their respective settings. Several examples have been configured for
    | you and you are free to add your own as your application requires.
    |
    | Laravel supports a variety of mail "transport" drivers that can be used
    | when delivering an email. You may specify which one you're using for
    | your mailers below. You may also add additional mailers if needed.
    |
    | Supported Transports:
    | - smtp: SMTP server (recommended for production)
    | - sendmail: Unix/Linux sendmail command
    | - mailgun: Mailgun email service
    | - ses: Amazon SES email service
    | - ses-v2: Amazon SES API v2
    | - postmark: Postmark email service
    | - resend: Resend email service
    | - log: Log to file (development)
    | - array: Store in array (testing)
    | - failover: Try multiple mailers in sequence
    | - roundrobin: Distribute across multiple mailers
    |
    */

    'mailers' => [

        /**
         * SMTP Mailer Configuration
         *
         * Use this for production email delivery via SMTP server.
         * For Oman-based mail servers, configure host/port accordingly.
         *
         * Environment variables required:
         * - MAIL_HOST: Mail server hostname
         * - MAIL_PORT: Mail server port (typically 587 for TLS)
         * - MAIL_USERNAME: SMTP authentication username
         * - MAIL_PASSWORD: SMTP authentication password
         * - MAIL_ENCRYPTION: Encryption method (tls or ssl)
         */
        'smtp' => [
            'transport' => 'smtp',
            // 'scheme' => env('MAIL_SCHEME', 'smtp'),
            // 'encryption' => env('MAIL_ENCRYPTION'),
            'url' => env('MAIL_URL'),
            'host' => env('MAIL_HOST', '127.0.0.1'),
            'port' => env('MAIL_PORT', 587),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            // EHLO domain - helps with SPF/DKIM validation
            'local_domain' => env(
                'MAIL_EHLO_DOMAIN',
                parse_url((string) env('APP_URL', 'http://localhost'), PHP_URL_HOST)
            ),
        ],

        /**
         * Amazon SES Configuration
         *
         * Use AWS SES for scalable, reliable email delivery.
         * Requires AWS credentials configured in AWS SDK.
         */
        'ses' => [
            'transport' => 'ses',
        ],

        /**
         * Postmark Configuration
         *
         * Use Postmark for transactional emails.
         * Requires POSTMARK_SECRET environment variable.
         */
        'postmark' => [
            'transport' => 'postmark',
            // Optional: Specify a message stream ID for organizing emails
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // Optional: Configure client timeout
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        /**
         * Resend Configuration
         *
         * Modern email service by the Vercel team.
         * Requires RESEND_API_KEY environment variable.
         */
        'resend' => [
            'transport' => 'resend',
        ],

        /**
         * Sendmail Configuration
         *
         * Use the system's sendmail binary (Unix/Linux only).
         * Useful for simple setups without external mail service.
         */
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        /**
         * Log Driver Configuration
         *
         * Store emails in application logs instead of sending.
         * Perfect for local development and testing.
         *
         * Emails are saved to: storage/logs/laravel.log
         *
         * To view sent emails in development:
         * ```
         * tail -f storage/logs/laravel.log | grep "Message ID:"
         * ```
         */
        'log' => [
            'transport' => 'log',
            // Optional: Use a specific log channel (default uses config('logging.default'))
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        /**
         * Array Driver Configuration
         *
         * Store emails in memory during test execution.
         * Used by Laravel's Mail fake for testing.
         *
         * Useful for:
         * - Unit tests: Assert emails are sent
         * - Feature tests: Verify email content
         * - Integration tests: Check mailables
         */
        'array' => [
            'transport' => 'array',
        ],

        /**
         * Failover Configuration
         *
         * Try multiple mailers in sequence.
         * If the first mailer fails, automatically try the next.
         *
         * Example: SMTP -> Log (ensures email is logged if delivery fails)
         * Useful for high-availability email systems.
         */
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            // Wait 60 seconds before retrying a failed mailer
            'retry_after' => 60,
        ],

        /**
         * Round Robin Configuration
         *
         * Distribute emails across multiple mailers.
         * Useful for load balancing across services.
         *
         * Example: Alternate between SES and Postmark
         */
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            // Wait 60 seconds before retrying after all mailers fail
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | You may wish for all emails sent by your application to be sent from
    | the same address. Here you may specify a name and address that is
    | used globally for all emails that are sent by your application.
    |
    | IMPORTANT: This address must be verified with your mail service provider
    | for proper delivery. Unverified addresses may result in bounce-backs.
    |
    | Environment variables:
    | - MAIL_FROM_ADDRESS: Sender's email address (required)
    | - MAIL_FROM_NAME: Sender's display name (optional, defaults to APP_NAME)
    |
    */

    'from' => [
        // The email address that will appear as the sender
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
        // The name that will appear as the sender display name
        'name' => env('MAIL_FROM_NAME', 'Majalis'),
    ],

];
