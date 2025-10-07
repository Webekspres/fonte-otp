# Fonnte OTP Package for Laravel

This package provides an easy way to send OTP (One-Time Password) via WhatsApp using the Fonnte API in Laravel applications. It supports Laravel 8, 9, 10, 11, and 12.

## Features

- Send OTP via WhatsApp using Fonnte API
- Automatic environment setup
- Configurable OTP expiry time
- Rate limiting to prevent spam
- Event system for OTP actions
- Easy to extend and customize
- Automatic retry mechanism for failed requests
- Compatible with Laravel 8-12
- Dynamic message formatting with custom variables

## Requirements

- PHP 7.4 or higher
- Laravel 8, 9, 10, 11, or 12
- GuzzleHttp client

## Installation

You can install the package via composer:

```bash
composer require webekspres/fonte-otp
```

After installation, run the following command to publish the configuration and migration files:

```bash
php artisan fonnte:install
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

The package will automatically add the following environment variables to your `.env` file:

```env
FONNTE_TOKEN=
FONNTE_OTP_EXPIRY=5
FONNTE_MESSAGE_TEMPLATE="Kode OTP Anda adalah {code}"
FONNTE_MAX_RETRIES=3
FONNTE_RETRY_DELAY=1000
FONNTE_OTP_MAX_ATTEMPTS=3
FONNTE_OTP_DECAY_MINUTES=10
FONNTE_COMPANY_NAME=Our Company
FONNTE_SUPPORT_EMAIL=support@example.com
FONNTE_APP_NAME=Our App
```

You need to fill in your Fonnte API token in the `FONNTE_TOKEN` variable.

### Publishing Configuration (Optional)

If you want to customize the configuration, you can publish the config file:

```bash
php artisan vendor:publish --provider="Webekspres\FonnteOtp\Providers\FonnteOtpServiceProvider" --tag="fonnte-otp-config"
```

## Complete Implementation Examples

### Basic Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use Webekspres\FonnteOtp\Facades\FonnteOtp;
use Illuminate\Http\Request;

class OtpController extends Controller
{
    /**
     * Send OTP to user's phone number
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string'
        ]);

        try {
            $response = FonnteOtp::send($request->phone);
            
            if ($response['success']) {
                return response()->json([
                    'message' => 'OTP sent successfully',
                    'data' => $response['data']
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to send OTP',
                    'error' => $response['error'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send OTP with custom variables
     */
    public function sendOtpWithVariables(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'name' => 'required|string'
        ]);

        // Custom variables for message personalization
        $variables = [
            'name' => $request->name,
            'action' => 'login verification'
        ];

        try {
            $response = FonnteOtp::send($request->phone, $variables);
            
            if ($response['success']) {
                return response()->json([
                    'message' => 'OTP sent successfully',
                    'data' => $response['data']
                ]);
            } else {
                return response()->json([
                    'message' => 'Failed to send OTP',
                    'error' => $response['error'] ?? 'Unknown error'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending OTP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify OTP for user's phone number
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'otp' => 'required|string|size:6'
        ]);

        $isValid = FonnteOtp::verify($request->phone, $request->otp);

        if ($isValid) {
            return response()->json([
                'message' => 'OTP verified successfully',
                'verified' => true
            ]);
        } else {
            return response()->json([
                'message' => 'Invalid OTP',
                'verified' => false
            ], 400);
        }
    }
}
```

### Protected Route with OTP Middleware

```php
// In routes/web.php or routes/api.php
use App\Http\Controllers\ProtectedController;

// Protect a route with OTP verification
Route::middleware(['verify.otp'])->post('/secure-action', [ProtectedController::class, 'handle']);

// Protect a route with both rate limiting and OTP verification
Route::middleware(['otp.rate', 'verify.otp'])->post('/sensitive-action', [ProtectedController::class, 'sensitive']);
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProtectedController extends Controller
{
    /**
     * Handle protected action (OTP verification is done by middleware)
     */
    public function handle(Request $request)
    {
        // At this point, OTP has been verified by middleware
        // You can safely proceed with the protected action
        
        return response()->json([
            'message' => 'Protected action completed successfully',
            'user_data' => [
                'phone' => $request->input('phone'),
                'action' => 'secure-action-completed'
            ]
        ]);
    }

    /**
     * Handle sensitive action with rate limiting
     */
    public function sensitive(Request $request)
    {
        // This action is protected by both rate limiting and OTP verification
        
        return response()->json([
            'message' => 'Sensitive action completed successfully',
            'user_data' => [
                'phone' => $request->input('phone'),
                'action' => 'sensitive-action-completed'
            ]
        ]);
    }
}
```

### Custom Event Listeners

```php
<?php

namespace App\Listeners;

use Webekspres\FonnteOtp\Events\OtpSent;
use Illuminate\Support\Facades\Log;

class LogOtpSent
{
    /**
     * Handle the event.
     */
    public function handle(OtpSent $event): void
    {
        Log::info('OTP sent to phone number: ' . $event->phoneNumber, [
            'otp' => $event->otp,
            'timestamp' => now()
        ]);
    }
}
```

Register the listener in `app/Providers/EventServiceProvider.php`:

```php
use Webekspres\FonnteOtp\Events\OtpSent;
use App\Listeners\LogOtpSent;

protected $listen = [
    OtpSent::class => [
        LogOtpSent::class,
    ],
];
```

### API Resource Implementation

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OtpResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'phone_number' => $this->phone_number,
            'expires_at' => $this->expires_at->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'is_expired' => $this->expires_at->isPast(),
        ];
    }
}
```

## Usage

### Sending OTP

```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

// Send OTP to a phone number
$response = FonnteOtp::send('+6281234567890');

// Send OTP with custom variables
$variables = [
    'name' => 'John Doe',
    'action' => 'account verification'
];
$response = FonnteOtp::send('+6281234567890', $variables);
```

### Verifying OTP

```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

// Verify OTP for a phone number
$isValid = FonnteOtp::verify('+6281234567890', '123456');
```

### Using the Rate Limiter Middleware

You can protect your OTP endpoints with the built-in rate limiter:

```php
Route::post('/send-otp', [OtpController::class, 'send'])->middleware('otp.rate');
```

### Using the OTP Verification Middleware

You can verify OTPs automatically with the built-in middleware:

```php
Route::post('/protected-endpoint', [ProtectedController::class, 'handle'])->middleware('verify.otp');
```

### Command Line Usage with Variables

```bash
# Send OTP with custom variables
php artisan fonnte:send-otp +6281234567890 --variables='{"name":"John","action":"login"}'
```

## Documentation

For more detailed information, please refer to the following documentation files:

- [API Documentation](docs/api.md) - Detailed documentation for all public methods
- [Configuration](docs/configuration.md) - Configuration options and environment variables
- [Events](docs/events.md) - Event system and how to listen to OTP events
- [Facade](docs/facade.md) - How to use the facade for convenient access
- [Installation](docs/installation.md) - Detailed installation instructions
- [Middleware](docs/middleware.md) - Middleware usage for protecting routes
- [Testing](docs/testing.md) - How to test the package functionality
- [Usage](docs/usage.md) - Comprehensive usage examples

## Events

The package fires the following events:

- `OtpGenerated` - When an OTP is generated
- `OtpSent` - When an OTP is successfully sent
- `OtpVerified` - When an OTP is verified

You can listen to these events in your `EventServiceProvider`:

```php
use Webekspres\FonnteOtp\Events\OtpSent;
use Webekspres\FonnteOtp\Listeners\LogOtpSent;

protected $listen = [
    OtpSent::class => [
        LogOtpSent::class,
    ],
];
```

## Security

- OTPs are stored encrypted in the database
- Rate limiting prevents spam
- Fonnte token is stored in environment variables only
- Automatic retry mechanism for failed requests

## Customization

### Message Template

You can customize the OTP message template by changing the `FONNTE_MESSAGE_TEMPLATE` environment variable:

```env
FONNTE_MESSAGE_TEMPLATE="Hello {name}, your verification code for {action} is {code}. Valid for {expiry} minutes."
```

The following placeholders are available:
- `{code}` - The OTP code
- `{expiry}` - The expiry time in minutes
- `{name}` - User's name (when provided)
- `{action}` - The action being performed (when provided)
- `{company_name}` - Company name from config
- `{support_email}` - Support email from config
- `{app_name}` - App name from config

### OTP Expiry

Change the OTP expiry time (in minutes) with the `FONNTE_OTP_EXPIRY` environment variable:

```env
FONNTE_OTP_EXPIRY=10
```

### Retry Settings

Configure retry settings for failed requests:

```env
FONNTE_MAX_RETRIES=5
FONNTE_RETRY_DELAY=2000
```

## Testing

To run tests, execute:

```bash
./vendor/bin/pest
```

## Developed By

Developed by [Fadhila36](https://github.com/Fadhila36)

## Powered By

Powered by [Webekspres](https://webekspres.id)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.