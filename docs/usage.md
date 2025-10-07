# Usage

## Sending OTP

To send an OTP to a phone number, use the facade:

```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

$response = FonnteOtp::send('+6281234567890');
```

The send method returns an array with the response from the Fonnte API:

```php
[
    'success' => true,
    'data' => [...] // API response data
]
```

If there's an error, it will throw an exception.

## Verifying OTP

To verify an OTP for a phone number:

```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

$isValid = FonnteOtp::verify('+6281234567890', '123456');
```

The verify method returns a boolean indicating whether the OTP is valid.

## Using the Service Directly

You can also inject the service directly:

```php
use Webekspres\FonnteOtp\Services\FonnteOtpService;

public function sendOtp(FonnteOtpService $fonnteOtp)
{
    $response = $fonnteOtp->send('+6281234567890');
    // ...
}
```

## Using the Rate Limiter Middleware

To prevent spam, you can use the built-in rate limiter middleware:

```php
Route::post('/send-otp', [OtpController::class, 'send'])->middleware('otp.rate');
```

## Using the OTP Verification Middleware

To automatically verify OTPs, you can use the built-in verification middleware:

```php
Route::post('/protected-endpoint', [ProtectedController::class, 'handle'])->middleware('verify.otp');
```

This middleware expects `phone` and `otp` parameters in the request.

## Events

The package fires events during the OTP process:

### OtpGenerated

Fired when an OTP is generated:

```php
use Webekspres\FonnteOtp\Events\OtpGenerated;

public function handle(OtpGenerated $event)
{
    // $event->phoneNumber
    // $event->otp
}
```

### OtpSent

Fired when an OTP is successfully sent:

```php
use Webekspres\FonnteOtp\Events\OtpSent;

public function handle(OtpSent $event)
{
    // $event->phoneNumber
    // $event->otp
}
```

### OtpVerified

Fired when an OTP is verified:

```php
use Webekspres\FonnteOtp\Events\OtpVerified;

public function handle(OtpVerified $event)
{
    // $event->phoneNumber
    // $event->otp
}
```

## Artisan Commands

### fonnte:install

Installs the package and publishes configuration:

```bash
php artisan fonnte:install
```

### fonnte:send-otp

Sends a test OTP to a phone number:

```bash
php artisan fonnte:send-otp +6281234567890
```

### fonnte:verify-otp

Verifies an OTP for a phone number:

```bash
php artisan fonnte:verify-otp +6281234567890 123456
```

## Error Handling

The package throws specific exceptions for different error conditions:

### InvalidPhoneNumberException

Thrown when the phone number format is invalid.

### OtpSendingFailedException

Thrown when there's an error sending the OTP via the Fonnte API.

Example of handling exceptions:

```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;
use Webekspres\FonnteOtp\Exceptions\InvalidPhoneNumberException;
use Webekspres\FonnteOtp\Exceptions\OtpSendingFailedException;

try {
    $response = FonnteOtp::send('+6281234567890');
} catch (InvalidPhoneNumberException $e) {
    // Handle invalid phone number
} catch (OtpSendingFailedException $e) {
    // Handle sending failure
}
```

## Retry Mechanism

The package includes an automatic retry mechanism for failed requests. By default, it will retry up to 3 times with a 1-second delay between attempts. You can configure these settings in the configuration file or environment variables.