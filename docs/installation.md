# Installation Guide

## Requirements

- PHP 7.4 or higher
- Laravel 8, 9, 10, 11, or 12
- Composer

## Installation Steps

### 1. Install the Package

Require the package using Composer:

```bash
composer require webekspres/fonte-otp
```

### 2. Run the Installation Command

After installing the package, run the installation command:

```bash
php artisan fonnte:install
```

This command will:
- Publish the configuration file to `config/fonnte-otp.php`
- Publish the migration files
- Add required environment variables to your `.env` file

### 3. Run Migrations

Run the database migrations to create the OTP codes table:

```bash
php artisan migrate
```

### 4. Configure Environment Variables

The installation command will automatically add the following variables to your `.env` file:

```env
FONNTE_TOKEN=
FONNTE_OTP_EXPIRY=5
FONNTE_MESSAGE_TEMPLATE="Kode OTP Anda adalah {code}"
FONNTE_MAX_RETRIES=3
FONNTE_RETRY_DELAY=1000
FONNTE_OTP_MAX_ATTEMPTS=3
FONNTE_OTP_DECAY_MINUTES=10
```

You need to fill in your Fonnte API token in the `FONNTE_TOKEN` variable.

### 5. Verify Installation

You can test if the package is installed correctly by running:

```bash
php artisan fonnte:send-otp +6281234567890
```

This will send a test OTP to the specified phone number.

---

Developed by [Fadhila36](https://github.com/Fadhila36)  
Powered by [Webekspres](https://webekspres.id)