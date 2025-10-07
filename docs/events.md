# Events Documentation

The package fires several events during OTP operations that you can listen to.

## OtpGenerated

Fired when an OTP is generated.

### Properties
- `phoneNumber`: The phone number the OTP was generated for
- `otp`: The generated OTP code

### Example Listener
```php
<?php

namespace App\Listeners;

use Webekspres\FonnteOtp\Events\OtpGenerated;

class LogOtpGenerated
{
    public function handle(OtpGenerated $event)
    {
        \Log::info('OTP generated for ' . $event->phoneNumber, [
            'otp' => $event->otp,
            'timestamp' => now()
        ]);
    }
}
```

## OtpSent

Fired when an OTP is successfully sent.

### Properties
- `phoneNumber`: The phone number the OTP was sent to
- `otp`: The sent OTP code

### Example Listener
```php
<?php

namespace App\Listeners;

use Webekspres\FonnteOtp\Events\OtpSent;

class LogOtpSent
{
    public function handle(OtpSent $event)
    {
        \Log::info('OTP sent to ' . $event->phoneNumber, [
            'otp' => $event->otp,
            'timestamp' => now()
        ]);
    }
}
```

## OtpVerified

Fired when an OTP is verified.

### Properties
- `phoneNumber`: The phone number the OTP was verified for
- `otp`: The verified OTP code

### Example Listener
```php
<?php

namespace App\Listeners;

use Webekspres\FonnteOtp\Events\OtpVerified;

class LogOtpVerified
{
    public function handle(OtpVerified $event)
    {
        \Log::info('OTP verified for ' . $event->phoneNumber, [
            'otp' => $event->otp,
            'timestamp' => now()
        ]);
    }
}
```

## Registering Event Listeners

Register your listeners in `app/Providers/EventServiceProvider.php`:

```php
use Webekspres\FonnteOtp\Events\OtpGenerated;
use Webekspres\FonnteOtp\Events\OtpSent;
use Webekspres\FonnteOtp\Events\OtpVerified;
use App\Listeners\LogOtpGenerated;
use App\Listeners\LogOtpSent;
use App\Listeners\LogOtpVerified;

protected $listen = [
    OtpGenerated::class => [
        LogOtpGenerated::class,
    ],
    OtpSent::class => [
        LogOtpSent::class,
    ],
    OtpVerified::class => [
        LogOtpVerified::class,
    ],
];
```