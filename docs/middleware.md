# Middleware Documentation

## OTP Verification Middleware

Middleware to verify OTP before allowing access to protected routes.

### Usage

```php
Route::middleware(['verify.otp'])->post('/protected-endpoint', [Controller::class, 'method']);
```

### Requirements

The middleware expects the following parameters in the request:
- `phone`: The phone number
- `otp`: The OTP code

### Response

If OTP verification fails, the middleware will return a JSON response with HTTP status 401:
```json
{
    "message": "Invalid OTP."
}
```

If phone number or OTP is missing, the middleware will return a JSON response with HTTP status 422:
```json
{
    "message": "Phone number and OTP are required."
}
```

## OTP Rate Limiter Middleware

Middleware to limit the number of OTP requests from the same phone number.

### Usage

```php
Route::middleware(['otp.rate'])->post('/send-otp', [Controller::class, 'method']);
```

### Configuration

The rate limiter can be configured in the `fonnte-otp.php` config file:
```php
'rate_limit' => [
    'max_attempts' => env('FONNTE_OTP_MAX_ATTEMPTS', 3),
    'decay_minutes' => env('FONNTE_OTP_DECAY_MINUTES', 10),
],
```

### Response

If rate limit is exceeded, the middleware will return a JSON response with HTTP status 429:
```json
{
    "message": "Too many attempts. Please try again later.",
    "retry_after": 600
}
```