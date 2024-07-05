# Simple Logger

A simple PSR-3 compliant logging utility for PHP.

## Installation

You can install the package via Composer:

```bash
composer require mafin/simple-logger
```

```php
use Mafin\SimpleLogger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('app.log', LogLevel::DEBUG);
$logger->info('This is an info message.');
$logger->error('This is an error message.');
```
