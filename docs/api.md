# API Documentation

## FonnteOtpService

The main service class for handling OTP operations.

### Methods

#### send(string $phoneNumber, array $variables = []): array

Send an OTP to a phone number via WhatsApp using Fonnte API.

**Parameters:**
- `$phoneNumber` (string): The phone number to send the OTP to in international format (+628123456789)
- `$variables` (array, optional): Custom variables to use in the message template

**Returns:**
- `array`: Response from the API with success status and data

**Throws:**
- `InvalidPhoneNumberException`: If the phone number format is invalid
- `OtpSendingFailedException`: If the OTP sending fails

**Example:**
```php
$service = new FonnteOtpService();
$response = $service->send('+628123456789');
// With custom variables
$response = $service->send('+628123456789', ['name' => 'John', 'action' => 'login']);
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
$service = new FonnteOtpService();
$isValid = $service->verify('+628123456789', '123456');
```

### Protected Methods

#### generateOtp(): string

Generate a random 6-digit OTP.

**Returns:**
- `string`: A 6-digit OTP code

#### sendViaFonnteWithRetry(string $phoneNumber, string $otp, array $variables = []): array

Send OTP via Fonnte API with retry logic.

**Parameters:**
- `$phoneNumber` (string): The phone number to send the OTP to
- `$otp` (string): The OTP code to send
- `$variables` (array): Custom variables to use in the message template

**Returns:**
- `array`: Response from the API

**Throws:**
- `OtpSendingFailedException`: If the OTP sending fails after all retries

#### sendViaFonnte(string $phoneNumber, string $otp, array $variables = []): array

Send OTP via Fonnte API.

**Parameters:**
- `$phoneNumber` (string): The phone number to send the OTP to
- `$otp` (string): The OTP code to send
- `$variables` (array): Custom variables to use in the message template

**Returns:**
- `array`: Response from the API

**Throws:**
- `OtpSendingFailedException`: If the OTP sending fails

#### formatMessage(string $otp, array $variables = []): string

Format the message with OTP and variables.

**Parameters:**
- `$otp` (string): The OTP code
- `$variables` (array): Custom variables to use in the message template

**Returns:**
- `string`: The formatted message

#### validatePhoneNumber(string $phoneNumber): string

Validate and format phone number.

**Parameters:**
- `$phoneNumber` (string): The phone number to validate

**Returns:**
- `string`: The formatted phone number

**Throws:**
- `InvalidPhoneNumberException`: If the phone number format is invalid

#### formatPhoneNumber(string $phoneNumber): string

Format phone number to international format.

**Parameters:**
- `$phoneNumber` (string): The phone number to format

**Returns:**
- `string`: The formatted phone number

---

Developed by [Fadhila36](https://github.com/Fadhila36)  
Powered by [Webekspres](https://webekspres.id)