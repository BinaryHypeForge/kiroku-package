[![Latest Version on Packagist](https://img.shields.io/packagist/v/binaryhype/kiroku.svg?style=flat-square)](https://packagist.org/packages/binaryhype/kiroku)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/binaryhypeforge/kiroku-package/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/binaryhypeforge/kiroku-package/actions?query=workflow%3Arun-tests+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/binaryhype/kiroku.svg?style=flat-square)](https://packagist.org/packages/binaryhype/kiroku)

# Kiroku – Exception forwarding for Laravel

Kiroku is a lightweight Laravel package that automatically forwards exceptions from your application to an external HTTP API. It enriches the exception with request and app context and supports optional queueing with retries/backoff. Use it to centralize error reporting in your own systems.

- Sends exception details to a configurable API endpoint with a Bearer token
- Includes request context (URL, method, IP, headers, sanitized input)
- Optional queueing with retries/backoff to avoid blocking requests
- One-liner setup for automatic reporting in Laravel 11/12
- Artisan command to send a test exception

## Requirements
- PHP ^8.2
- Laravel ^12.0 (works with the streamlined bootstrap/app.php)

## Installation

Install via Composer:

```bash
composer require binaryhype/kiroku
```

Publish the config file:

```bash
php artisan vendor:publish --tag="kiroku-config"
```

### Configuration
Kiroku reads its configuration from config/kiroku.php (published file). Set the following environment variables in your .env:

```env
KIROKU_API_URL=https://your-api.example.com/exceptions
KIROKU_API_BEARER_TOKEN=your-bearer-token
KIROKU_QUEUE_ENABLED=false
```

Config reference (config/kiroku.php):

```php
return [
    'api' => [
        'url' => env('KIROKU_API_URL', ''),
        'bearer_token' => env('KIROKU_API_BEARER_TOKEN', ''),
    ],
    'queue' => [
        'enabled' => env('KIROKU_QUEUE_ENABLED', false),
    ],
];
```

## Quick start (automatic exception reporting)
With Laravel 11/12, wire Kiroku into the Exceptions configuration in your bootstrap/app.php:

```php
use BinaryHype\Kiroku\Kiroku;
use Illuminate\Foundation\Configuration\Exceptions;

return Application::configure(basePath: dirname(__DIR__))
    ->withExceptions(function (Exceptions $exceptions) {
        app(Kiroku::class)->handles($exceptions);
    })
    // ...
    ->create();
```

From now on, every reportable exception will be sent to your configured API. If KIROKU_QUEUE_ENABLED=true, Kiroku will dispatch a queued job (with retries/backoff). Otherwise, it will send the HTTP request immediately.

## What gets sent
Kiroku posts JSON to your API containing:
- Exception: code, message, file, line, trace, exception_class
- Request (if available): url, method, ip, user_agent, headers, input (with password, password_confirmation, token, _token removed)
- App: app name, app url, environment, timestamp

## Manual usage
You can also log a specific exception manually:

```php
use BinaryHype\Kiroku\Kiroku;

try {
    // ...
} catch (\Throwable $e) {
    Kiroku::logException($e);
}
```

A facade alias Kiroku is also registered, so you can import BinaryHype\Kiroku\Facades\Kiroku if you prefer.

## CLI: Send a test exception
Run the built-in test command to verify your configuration:

```bash
php artisan kiroku:test
```

You should see a success message on 200 OK responses, or an error message if the API rejects the request.

## Queueing details
When queueing is enabled, Kiroku dispatches BinaryHype\\Kiroku\\Jobs\\SendExceptionToApiJob with:
- 3 tries
- Backoff: [10, 30, 60] seconds

To prevent infinite loops, an internal container flag is used while processing.

## Testing locally
This package uses Pest. To run the package tests:

```bash
composer test
```

## Changelog
Please see [CHANGELOG](CHANGELOG.md) for recent changes.

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities
Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits
- [Tobias Kokesch](https://github.com/BinaryHypeForge)
- [All Contributors](../../contributors)

## License
The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
