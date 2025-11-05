# Simple Logger

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://www.php.net/)
[![PSR-3](https://img.shields.io/badge/PSR--3-compliant-green)](https://www.php-fig.org/psr/psr-3/)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

A simple, lightweight, and extensible PSR-3 compliant logging library for PHP 8.4+, built with SOLID principles and Dependency Injection support.

## Features

- ✅ **PSR-3 Compliant** - Fully implements PSR-3 LoggerInterface
- 🏗️ **SOLID Architecture** - Clean separation of concerns with interfaces
- 💉 **Dependency Injection** - Easily extensible with custom writers and formatters
- 🔒 **Thread-Safe** - File locking for concurrent writes
- 🎯 **Context Interpolation** - Replace placeholders with context values
- 📦 **Zero Dependencies** - Only requires `psr/log`
- ✨ **Type-Safe** - Full PHP 8.3+ type declarations
- 🧪 **Well Tested** - 100% test coverage with PHPUnit
- 📊 **Static Analysis** - PHPStan level max compliant

## Installation

Install via Composer:

```bash
composer require mafin/simple-logger
```

## Requirements

- PHP 8.4 or higher
- psr/log ^3.0

## Quick Start

### Basic Usage

```php
use Mafin\SimpleLogger\Logger;
use Psr\Log\LogLevel;

// Create a logger that writes to a file
$logger = new Logger('app.log', LogLevel::DEBUG);

// Log messages at different levels
$logger->info('This is an info message.');
$logger->error('This is an error message.');
$logger->debug('Debug information');
```

### Context Interpolation

```php
$logger->info('User {username} logged in from {ip}', [
    'username' => 'john_doe',
    'ip' => '192.168.1.1',
]);
// Output: [2025-11-05 12:34:56] [info] User john_doe logged in from 192.168.1.1
```

### Dependency Injection

```php
use Mafin\SimpleLogger\Infrastructure\FileLogWriter;
use Mafin\SimpleLogger\Infrastructure\DefaultLogFormatter;
use Mafin\SimpleLogger\Logger;
use Psr\Log\LogLevel;

// Custom writer
$writer = new FileLogWriter('logs/app.log');

// Custom formatter (optional)
$formatter = new DefaultLogFormatter();

// Inject dependencies
$logger = new Logger($writer, LogLevel::INFO, $formatter);
```

### Custom Writer

Implement your own log writer (database, API, etc.):

```php
use Mafin\SimpleLogger\Contract\LogWriterInterface;

class DatabaseLogWriter implements LogWriterInterface
{
    public function __construct(private PDO $pdo) {}

    public function write(string $message): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO logs (message) VALUES (?)');
        $stmt->execute([$message]);
    }
}

$logger = new Logger(new DatabaseLogWriter($pdo), LogLevel::INFO);
```

### Custom Formatter

```php
use Mafin\SimpleLogger\Contract\LogFormatterInterface;

class JsonFormatter implements LogFormatterInterface
{
    public function format(string $level, string|\Stringable $message, array $context = []): string
    {
        return json_encode([
            'timestamp' => time(),
            'level' => $level,
            'message' => (string) $message,
            'context' => $context,
        ]) . PHP_EOL;
    }
}

$logger = new Logger('app.log', LogLevel::DEBUG, new JsonFormatter());
```

## Supported Log Levels

All PSR-3 log levels are supported:

- `emergency` - System is unusable
- `alert` - Action must be taken immediately
- `critical` - Critical conditions
- `error` - Error conditions
- `warning` - Warning conditions
- `notice` - Normal but significant condition
- `info` - Informational messages
- `debug` - Debug-level messages

## Architecture

The library follows SOLID principles with clean separation:

```
src/
├── Contract/              # Interfaces (abstraction layer)
│   ├── LogWriterInterface.php
│   └── LogFormatterInterface.php
├── Infrastructure/        # Concrete implementations
│   ├── FileLogWriter.php
│   └── DefaultLogFormatter.php
└── Logger.php            # Main logger orchestration
```

## Development

### Running Tests

```bash
composer test
```

### Code Style Check

```bash
composer ecs
```

### Static Analysis

```bash
composer phpstan
```

### All Checks

```bash
composer check
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

## Credits

- [Petr Jenin](https://github.com/mafin)

## Security

If you discover any security related issues, please email petr.enin@gmail.com instead of using the issue tracker.
