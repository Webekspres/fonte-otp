<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Fonnte API Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Fonnte API settings.
    |
    */

    'token' => env('FONNTE_TOKEN'),

    'base_url' => env('FONNTE_BASE_URL', 'https://api.fonnte.com'),

    /*
    |--------------------------------------------------------------------------
    | OTP Settings
    |--------------------------------------------------------------------------
    |
    | Here you may configure the OTP settings.
    |
    */

    'otp_expiry' => env('FONNTE_OTP_EXPIRY', 5), // in minutes

    'message_template' => env('FONNTE_MESSAGE_TEMPLATE', 'Kode OTP Anda adalah {code}'),

    /*
    |--------------------------------------------------------------------------
    | Dynamic Message Variables
    |--------------------------------------------------------------------------
    |
    | Here you can configure additional variables that can be used in your
    | message templates for more personalized messages.
    |
    */

    'message_variables' => [
        'company_name' => env('FONNTE_COMPANY_NAME', 'Our Company'),
        'support_email' => env('FONNTE_SUPPORT_EMAIL', 'support@example.com'),
        'app_name' => env('FONNTE_APP_NAME', 'Our App'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Retry Settings
    |--------------------------------------------------------------------------
    |
    | Configure retry settings for OTP sending.
    |
    */

    'max_retries' => env('FONNTE_MAX_RETRIES', 3),

    'retry_delay' => env('FONNTE_RETRY_DELAY', 1000), // in milliseconds

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting for OTP requests.
    |
    */

    'rate_limit' => [
        'max_attempts' => env('FONNTE_OTP_MAX_ATTEMPTS', 3),
        'decay_minutes' => env('FONNTE_OTP_DECAY_MINUTES', 10),
    ],
];