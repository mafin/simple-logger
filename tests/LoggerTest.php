<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Tests;

use Mafin\SimpleLogger\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
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
}
