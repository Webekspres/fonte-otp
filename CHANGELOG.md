# Changelog

All notable changes to the `fonnte-otp` package will be documented in this file.

## 1.1.0 - 2025-10-07

### Added
- Laravel 8-12 compatibility
- Automatic retry mechanism for failed requests
- New artisan command: `fonnte:verify-otp`
- OTP verification middleware
- Additional configuration options for retries and rate limiting
- Enhanced documentation
- Developed by [Fadhila36](https://github.com/Fadhila36)
- Powered by [Webekspres](https://webekspres.id)

### Changed
- Updated composer.json to support Laravel 8-12
- Improved service provider compatibility across Laravel versions
- Enhanced error handling with more specific exceptions

## 1.0.0 - 2025-10-07

### Added
- Initial release of the Fonnte OTP package
- Send OTP via WhatsApp using Fonnte API
- Verify OTP codes
- Automatic environment setup
- Configurable OTP expiry time
- Rate limiting to prevent spam
- Event system for OTP actions
- Artisan commands for installation and testing
- Comprehensive documentation