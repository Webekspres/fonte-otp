# Facade Documentation

## FonnteOtp Facade

The facade provides a convenient way to access the OTP service methods.

### Methods

#### send(string $phoneNumber, array $variables = []): array

Send an OTP to a phone number via WhatsApp using Fonnte API.

**Parameters:**
- `$phoneNumber` (string): The phone number to send the OTP to in international format (+628123456789)
- `$variables` (array, optional): Custom variables to use in the message template

**Returns:**
- `array`: Response from the API with success status and data

**Example:**
```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

$response = FonnteOtp::send('+628123456789');
// With custom variables
$response = FonnteOtp::send('+628123456789', ['name' => 'John', 'action' => 'login']);
```

#### verify(string $phoneNumber, string $otp): bool

Verify an OTP for a phone number.

**Parameters:**
- `$phoneNumber` (string): The phone number to verify the OTP for
- `$otp` (string): The OTP code to verify

**Returns:**
- `bool`: True if the OTP is valid, false otherwise

**Example:**
```php
use Webekspres\FonnteOtp\Facades\FonnteOtp;

$isValid = FonnteOtp::verify('+628123456789', '123456');
```