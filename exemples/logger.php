<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Mafin\SimpleLogger\Contract\LogFormatterInterface;
use Mafin\SimpleLogger\Infrastructure\FileLogWriter;
use Mafin\SimpleLogger\Logger;
use Psr\Log\LogLevel;

// Example 1: Basic usage (backward compatible)
echo "=== Example 1: Basic Usage ===\n";
$logger = new Logger('test.log', LogLevel::DEBUG);
$logger->info('This is an info log message.');
$logger->error('This is an error log message.');

echo file_get_contents('test.log');
unlink('test.log');

// Example 2: Context interpolation
echo "\n=== Example 2: Context Interpolation ===\n";
$logger = new Logger('test.log', LogLevel::DEBUG);
$logger->info('User {username} logged in from {ip}', [
    'username' => 'john_doe',
    'ip' => '192.168.1.1',
]);
$logger->warning('Failed login attempt for {username}', ['username' => 'admin']);

echo file_get_contents('test.log');
unlink('test.log');

// Example 3: Using Dependency Injection with a custom writer
echo "\n=== Example 3: Custom Writer (Dependency Injection) ===\n";
$writer = new FileLogWriter('custom.log');
$logger = new Logger($writer, LogLevel::INFO);
$logger->info('Using custom writer via DI');
$logger->debug('This debug message will not be logged');

echo file_get_contents('custom.log');
unlink('custom.log');

// Example 4: Custom formatter
echo "\n=== Example 4: Custom Formatter ===\n";
$customFormatter = new class implements LogFormatterInterface {
    public function format(string $level, string|\Stringable $message, array $context = []): string
    {
        return strtoupper($level) . ': ' . $message . PHP_EOL;
    }
};

$logger = new Logger('test.log', LogLevel::DEBUG, $customFormatter);
$logger->error('Custom formatted error message');

echo file_get_contents('test.log');
unlink('test.log');

echo "\nDone.\n";
