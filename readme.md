# Laravel OTP ▲

[![Tests](https://github.com/turahe/laravel-otp/workflows/CI/badge.svg)](https://github.com/turahe/laravel-otp/actions)
[![Code Quality](https://github.com/turahe/laravel-otp/workflows/CI/badge.svg)](https://github.com/turahe/laravel-otp/actions)
[![Latest Stable Version](https://poser.pugx.org/turahe/otp/v/stable)](https://packagist.org/packages/turahe/otp)
[![Total Downloads](https://poser.pugx.org/turahe/otp/downloads)](https://packagist.org/packages/turahe/otp)
[![License](https://poser.pugx.org/turahe/otp/license)](https://packagist.org/packages/turahe/otp)

## Introduction 🖖

A robust Laravel package for generating and validating OTPs (One Time Passwords) with comprehensive test coverage and modern CI/CD pipeline. Perfect for authentication systems, email verification, and secure access control.

## Features ✨

- 🔐 **Secure OTP Generation**: 6-digit numeric tokens with configurable expiry
- 📧 **Email Integration**: Built-in email sending with customizable templates
- 🧪 **Comprehensive Testing**: 87+ tests with 100% coverage of core functionality
- 🚀 **Modern CI/CD**: GitHub Actions with PHP 8.2-8.4 and Laravel 10-12 support
- 📱 **Flexible Identity**: Support for email, phone numbers, or any string identifier
- ⏰ **Automatic Cleanup**: Scheduled cleanup of expired tokens
- 🎨 **PSR-12 Compliant**: Clean, maintainable code following Laravel best practices

## Requirements 📋

- **PHP**: ^8.2
- **Laravel**: ^10.0 || ^11.0 || ^12.0
- **Database**: MySQL, PostgreSQL, SQLite, or SQL Server

## Installation 💽

### 1. Install via Composer

```bash
composer require turahe/otp
```

### 2. Add Service Provider

Add to `config/app.php` providers array:

```php
'providers' => [
    // ...
    Turahe\Otp\OtpServiceProvider::class,
],
```

### 3. Add Facade Alias (Optional)

Add to `config/app.php` aliases array:

```php
'aliases' => [
    // ...
    'Otp' => Turahe\Otp\Facades\Otp::class,
],
```

### 4. Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Turahe\Otp\OtpServiceProvider"
```

### 5. Run Migrations

```bash
php artisan migrate
```

## Configuration ⚙️

The package configuration file (`config/otp.php`) allows you to customize:

```php
return [
    // Token expiry time in minutes
    'expires' => 15,
    
    // Database table name
    'table' => 'otp_tokens',
    
    // Password generator type (string, numeric, numeric-no-0)
    'password_generator' => 'numeric',
    
    // Default notification channels
    'default_channels' => 'mail',
];
```

## Usage 🧨

### Basic OTP Generation

```php
use Turahe\Otp\Facades\Otp;

// Generate OTP for email (default 15 minutes expiry)
$otp = Otp::generate('user@example.com');

// Generate OTP with custom expiry (10 minutes)
$otp = Otp::generate('user@example.com', 10);

// Generate OTP for phone number
$otp = Otp::generate('+1234567890', 5);
```

### OTP Validation

```php
// Validate OTP
$isValid = Otp::validate('user@example.com', '123456');

if ($isValid) {
    // OTP is valid and has been consumed
    echo "OTP verified successfully!";
} else {
    // OTP is invalid or expired
    echo "Invalid or expired OTP";
}
```

### Email Integration

```php
use Turahe\Otp\Jobs\SendOtp;

// Send OTP via email
$otp = Otp::generate('user@example.com');
dispatch(new SendOtp('user@example.com', $otp));
```

### Custom Email Templates

The package includes a default email template at `resources/views/emails/otp.blade.php`. You can customize it by publishing the views:

```bash
php artisan vendor:publish --tag=otp-views
```

### Cleanup Expired Tokens

```bash
# Manual cleanup
php artisan otp:clean

# Scheduled cleanup (add to app/Console/Kernel.php)
protected function schedule(Schedule $schedule)
{
    $schedule->command('otp:clean')->daily();
}
```

## Testing 🧪

The package includes comprehensive test coverage:

```bash
# Run all tests
composer test

# Run specific test suites
composer test tests/HelperTest.php
composer test tests/Jobs/SendOtpTest.php
composer test tests/Services/TokenTest.php

# Run with coverage report
composer test -- --coverage-html coverage/
```

### Test Coverage

- **Helper Functions**: Phone validation, email provider extraction, disposable email detection
- **SendOtp Job**: Email queuing, parameter handling, edge cases
- **Token Service**: OTP generation, validation, expiry handling, serialization
- **Integration Tests**: Full workflow testing with database interactions

## CI/CD Pipeline 🚀

### Continuous Integration

The GitHub Actions workflow runs on every push and pull request:

- **Matrix Testing**: PHP 8.2, 8.3, 8.4 × Laravel 10, 11, 12
- **Code Quality**: PHP CS Fixer (PSR-12) and PHPStan static analysis
- **Security**: Composer security audit
- **Validation**: Composer.json validation and lock file checks

### Release Management

Automated releases are created when semantic version tags are pushed:

```bash
git tag v1.2.0
git push origin v1.2.0
```

### Local Development

Run the same checks locally:

```bash
# Code quality checks
composer cs-check
composer stan

# Fix code style
composer cs-fix

# Security audit
composer audit

# Full test suite
composer test
```

## API Reference 📚

### Otp Facade

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `generate()` | `string $identity, int $expiresAt = 15` | `OtpToken` | Generate new OTP |
| `validate()` | `string $identity, string $token` | `bool` | Validate OTP |

### Token Service

| Method | Parameters | Returns | Description |
|--------|------------|---------|-------------|
| `identity()` | - | `mixed` | Get token identity |
| `token()` | - | `string` | Get token value |
| `expired()` | - | `bool` | Check if token expired |
| `timeLeft()` | - | `int` | Get seconds until expiry |

### Helper Functions

| Function | Parameters | Returns | Description |
|----------|------------|---------|-------------|
| `validation_number()` | `string $number, string $country = 'ID'` | `bool` | Validate phone number |
| `format_number()` | `string $number, string $country = 'ID'` | `string` | Format phone number |
| `get_email_provider()` | `string $email` | `string` | Extract email provider |
| `validate_email()` | `string $email` | `bool` | Check if email is disposable |

## Examples 📝

### Authentication Flow

```php
// 1. Generate OTP for login
$otp = Otp::generate($user->email, 10);

// 2. Send OTP via email
dispatch(new SendOtp($user->email, $otp));

// 3. User enters OTP
$userOtp = request('otp');

// 4. Validate OTP
if (Otp::validate($user->email, $userOtp)) {
    // Login successful
    Auth::login($user);
    return redirect()->intended('/dashboard');
} else {
    // Invalid OTP
    return back()->withErrors(['otp' => 'Invalid or expired OTP']);
}
```

### Phone Number Validation

```php
use Turahe\Otp\Helpers;

// Validate Indonesian phone number
$phone = '+6281234567890';
if (validation_number($phone, 'ID')) {
    $formatted = format_number($phone, 'ID');
    // +62 812-3456-7890
}
```

### Email Provider Detection

```php
use Turahe\Otp\Helpers;

$email = 'user@gmail.com';
$provider = get_email_provider($email);
// Returns: 'gmail'

// Check if disposable email
if (validate_email($email)) {
    // Email is not disposable
} else {
    // Email is disposable
}
```

## Contributing 🤝

We welcome contributions! Please see our contributing guidelines:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass (`composer test`)
6. Commit your changes (`git commit -m 'Add amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Setup

```bash
# Clone repository
git clone https://github.com/turahe/laravel-otp.git
cd laravel-otp

# Install dependencies
composer install

# Run tests
composer test

# Check code quality
composer cs-check
composer stan
```

## Security 🔒

If you discover any security-related issues, please email security@turahe.dev instead of using the issue tracker.

## License 📄

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support 💬

- **Documentation**: [GitHub Wiki](https://github.com/turahe/laravel-otp/wiki)
- **Issues**: [GitHub Issues](https://github.com/turahe/laravel-otp/issues)
- **Discussions**: [GitHub Discussions](https://github.com/turahe/laravel-otp/discussions)

## Changelog 📋

See [CHANGELOG.md](CHANGELOG.md) for a detailed history of changes.

---

**Made with ❤️ by [Turahe](https://github.com/turahe)**