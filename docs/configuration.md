# Configuration

## Environment Variables

The package automatically adds the following environment variables to your `.env` file:

```env
FONNTE_TOKEN=
FONNTE_OTP_EXPIRY=5
FONNTE_MESSAGE_TEMPLATE="Kode OTP Anda adalah {code}"
FONNTE_MAX_RETRIES=3
FONNTE_RETRY_DELAY=1000
FONNTE_OTP_MAX_ATTEMPTS=3
FONNTE_OTP_DECAY_MINUTES=10
```

### FONNTE_TOKEN

This is your Fonnte API token. You can get it from your Fonnte dashboard.

### FONNTE_OTP_EXPIRY

The expiry time for OTP codes in minutes. Default is 5 minutes.

### FONNTE_MESSAGE_TEMPLATE

The template for the OTP message. The `{code}` placeholder will be replaced with the actual OTP.

### FONNTE_MAX_RETRIES

The maximum number of retries for sending OTP. Default is 3.

### FONNTE_RETRY_DELAY

The delay between retries in milliseconds. Default is 1000ms (1 second).

### FONNTE_OTP_MAX_ATTEMPTS

The maximum number of OTP requests per decay period. Default is 3.

### FONNTE_OTP_DECAY_MINUTES

The decay period for rate limiting in minutes. Default is 10 minutes.

## Publishing Configuration

If you want to customize the package configuration, you can publish the configuration file:

```bash
php artisan vendor:publish --provider="Webekspres\FonnteOtp\Providers\FonnteOtpServiceProvider" --tag="fonnte-otp-config"
```

This will create a `config/fonnte-otp.php` file in your application.

## Configuration File Options

The configuration file contains the following options:

```php
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
```

## Rate Limiting Configuration

You can configure rate limiting for OTP requests by setting these environment variables:

```env
FONNTE_OTP_MAX_ATTEMPTS=3
FONNTE_OTP_DECAY_MINUTES=10
```

This will limit users to 3 OTP requests per 10 minutes.