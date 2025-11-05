<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Tests;

use Mafin\SimpleLogger\Contract\LogFormatterInterface;
use Mafin\SimpleLogger\Contract\LogWriterInterface;
use Mafin\SimpleLogger\Infrastructure\DefaultLogFormatter;
use Mafin\SimpleLogger\Infrastructure\FileLogWriter;
use Mafin\SimpleLogger\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use RuntimeException;
use Stringable;

final class LoggerTest extends TestCase
{
    protected string $logFile = 'test.log';

    protected function tearDown(): void
    {
        if (file_exists($this->logFile)) {
            unlink($this->logFile);
        }
    }

    public function testLog(): void
    {
        $logger = new Logger($this->logFile, LogLevel::DEBUG);
        $logger->info('This is an info log message.');
        $logger->error('This is an error log message.');

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('This is an info log message.', $content);
        $this->assertStringContainsString('This is an error log message.', $content);
    }

    public function testLogLevelFiltering(): void
    {
        $logger = new Logger($this->logFile, LogLevel::ERROR);
        $logger->info('This is an info log message.');
        $logger->error('This is an error log message.');

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringNotContainsString('This is an info log message.', $content);
        $this->assertStringContainsString('This is an error log message.', $content);
    }

    public function testContextInterpolation(): void
    {
        $logger = new Logger($this->logFile, LogLevel::DEBUG);
        $logger->info('User {username} logged in from {ip}', [
            'username' => 'john_doe',
            'ip' => '192.168.1.1',
        ]);

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('User john_doe logged in from 192.168.1.1', $content);
        $this->assertStringNotContainsString('{username}', $content);
        $this->assertStringNotContainsString('{ip}', $content);
    }

    public function testContextInterpolationWithComplexTypes(): void
    {
        $logger = new Logger($this->logFile, LogLevel::DEBUG);
        $logger->info('Data: {data}, Object: {object}, Null: {null}, Bool: {bool}', [
            'data' => ['key' => 'value'],
            'object' => new class() {
            },
            'null' => null,
            'bool' => true,
        ]);

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('"key":"value"', $content);
        $this->assertStringContainsString('[object', $content);
        $this->assertStringContainsString('null', $content);
        $this->assertStringContainsString('true', $content);
    }

    public function testStringableMessage(): void
    {
        $logger = new Logger($this->logFile, LogLevel::DEBUG);
        $message = new class() implements Stringable {
            public function __toString(): string
            {
                return 'Stringable message';
            }
        };

        $logger->info($message);

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('Stringable message', $content);
    }

    public function testInvalidLogLevel(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid log level');

        $logger = new Logger($this->logFile);
        $logger->log('invalid_level', 'test message');
    }

    public function testAllLogLevels(): void
    {
        $logger = new Logger($this->logFile, LogLevel::DEBUG);

        $logger->emergency('emergency');
        $logger->alert('alert');
        $logger->critical('critical');
        $logger->error('error');
        $logger->warning('warning');
        $logger->notice('notice');
        $logger->info('info');
        $logger->debug('debug');

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);

        $this->assertStringContainsString('[emergency]', $content);
        $this->assertStringContainsString('[alert]', $content);
        $this->assertStringContainsString('[critical]', $content);
        $this->assertStringContainsString('[error]', $content);
        $this->assertStringContainsString('[warning]', $content);
        $this->assertStringContainsString('[notice]', $content);
        $this->assertStringContainsString('[info]', $content);
        $this->assertStringContainsString('[debug]', $content);
    }

    public function testCustomWriter(): void
    {
        $messages = [];
        $writer = new class($messages) implements LogWriterInterface {
            public function __construct(private array &$messages)
            {
            }

            public function write(string $message): void
            {
                $this->messages[] = $message;
            }
        };

        $logger = new Logger($writer);
        $logger->info('Test message');

        $this->assertCount(1, $messages);
        $this->assertStringContainsString('Test message', $messages[0]);
    }

    public function testCustomFormatter(): void
    {
        $formatter = new class() implements LogFormatterInterface {
            public function format(string $level, string|Stringable $message, array $context = []): string
            {
                return "CUSTOM: {$level} - {$message}\n";
            }
        };

        $logger = new Logger($this->logFile, LogLevel::DEBUG, $formatter);
        $logger->info('Test');

        $this->assertFileExists($this->logFile);
        $content = file_get_contents($this->logFile);
        $this->assertStringContainsString('CUSTOM: info - Test', $content);
    }

    public function testFileWriterCreatesDirectory(): void
    {
        $logFile = 'logs/subdir/test.log';
        $logger = new Logger($logFile);
        $logger->info('Test');

        $this->assertFileExists($logFile);

        unlink($logFile);
        rmdir('logs/subdir');
        rmdir('logs');
    }

    public function testFileWriterThrowsExceptionOnWriteFailure(): void
    {
        $writer = new FileLogWriter('/invalid/path/that/cannot/exist/test.log');

        $this->expectException(RuntimeException::class);
        // Error handler catches mkdir() error and throws it directly
        $this->expectExceptionMessageMatches('/mkdir\(\)|Failed to create log directory/');

        $writer->write('Test message');
    }

    public function testFormatterInterpolation(): void
    {
        $formatter = new DefaultLogFormatter();
        $result = $formatter->format(
            LogLevel::INFO,
            'User {user} performed action {action}',
            ['user' => 'admin', 'action' => 'login'],
        );

        $this->assertStringContainsString('User admin performed action login', $result);
        $this->assertStringContainsString('[info]', $result);
    }
}
