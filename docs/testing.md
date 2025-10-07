# Testing Guide

This guide explains how to test the OTP package functionality.

## Running Tests

To run all tests:

```bash
./vendor/bin/pest
```

To run specific test files:

```bash
./vendor/bin/pest tests/Unit/FonnteOtpServiceTest.php
```

## Test Structure

The package includes both unit and feature tests:

### Unit Tests

Located in `tests/Unit/`:
- `FonnteOtpServiceTest.php` - Tests for the main service class

### Feature Tests

Located in `tests/Feature/`:
- `OtpMiddlewareTest.php` - Tests for the OTP verification middleware
- `OtpRateLimiterTest.php` - Tests for the rate limiter middleware

## Mocking the Fonnte API

To test without making actual API calls, you can mock the HTTP client:

```php
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

// Create a mock response
$mock = new MockHandler([
    new Response(200, [], json_encode([
        'status' => true,
        'message' => 'Message sent successfully'
    ]))
]);

$handlerStack = HandlerStack::create($mock);
$client = new Client(['handler' => $handlerStack]);

// Use reflection to inject the mock client into the service
$service = new FonnteOtpService();
$reflection = new \ReflectionClass($service);
$clientProperty = $reflection->getProperty('client');
$clientProperty->setAccessible(true);
$clientProperty->setValue($service, $mockClient);
```

## Testing with Laravel Application

When testing within a Laravel application:

1. Install the package as described in the installation guide
2. Publish and run migrations
3. Create test routes and controllers
4. Use the provided middleware and facades in your tests

### Example Feature Test

```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtpFeatureTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_send_and_verify_otp()
    {
        // Send OTP
        $response = $this->postJson('/api/send-otp', [
            'phone' => '+628123456789'
        ]);

        $response->assertStatus(200);

        // In a real test, you would retrieve the actual OTP
        // from the database or mock it
        
        // Verify OTP
        $response = $this->postJson('/api/verify-otp', [
            'phone' => '+628123456789',
            'otp' => '123456' // Use actual OTP in real test
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'verified' => true
                 ]);
    }
}
```

## Testing Edge Cases

### Invalid Phone Numbers

```php
/** @test */
public function it_rejects_invalid_phone_numbers()
{
    $this->expectException(InvalidPhoneNumberException::class);
    
    $service = new FonnteOtpService();
    $service->send('invalid-phone-number');
}
```

### Network Failures

```php
/** @test */
public function it_handles_network_failures()
{
    $this->expectException(OtpSendingFailedException::class);
    
    // Mock a network failure
    // ... implementation
}
```

### Rate Limiting

```php
/** @test */
public function it_respects_rate_limits()
{
    $phoneNumber = '+628123456789';
    
    // Make requests up to the limit
    // ... implementation
    
    // Next request should be rate limited
    $response = $this->postJson('/api/send-otp', [
        'phone' => $phoneNumber
    ]);
    
    $response->assertStatus(429);
}
```

## Continuous Integration

For CI/CD pipelines, ensure you have the required dependencies:

```yaml
# Example GitHub Actions workflow
name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v2
    
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.1'
        extensions: mbstring, pdo, sqlite
        coverage: none
    
    - name: Install dependencies
      run: composer install --prefer-dist --no-progress --no-suggest
    
    - name: Run tests
      run: ./vendor/bin/pest
```