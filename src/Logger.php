<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger;

use Mafin\SimpleLogger\Contract\LogFormatterInterface;
use Mafin\SimpleLogger\Contract\LogWriterInterface;
use Mafin\SimpleLogger\Infrastructure\DefaultLogFormatter;
use Mafin\SimpleLogger\Infrastructure\FileLogWriter;
use Psr\Log\AbstractLogger;
use Psr\Log\InvalidArgumentException;
use Psr\Log\LogLevel;
use Stringable;

final class Logger extends AbstractLogger
{
    protected const LEVELS = [
        LogLevel::EMERGENCY => 0,
        LogLevel::ALERT => 1,
        LogLevel::CRITICAL => 2,
        LogLevel::ERROR => 3,
        LogLevel::WARNING => 4,
        LogLevel::NOTICE => 5,
        LogLevel::INFO => 6,
        LogLevel::DEBUG => 7,
    ];

    private readonly LogWriterInterface $writer;
    private readonly LogFormatterInterface $formatter;

    public function __construct(
        string|LogWriterInterface $logFileOrWriter,
        private readonly string $logLevel = LogLevel::DEBUG,
        ?LogFormatterInterface $formatter = null,
    ) {
        // Backward compatibility: accept string path or LogWriter instance
        $this->writer = is_string($logFileOrWriter)
            ? new FileLogWriter($logFileOrWriter)
            : $logFileOrWriter;

        $this->formatter = $formatter ?? new DefaultLogFormatter();
    }

    /**
     * @param string $level
     * @param array<string, mixed> $context
     */
    public function log(
        $level,
        string|Stringable $message,
        array $context = [],
    ): void {
        if (isset(self::LEVELS[$level]) === false) {
            throw new InvalidArgumentException('Invalid log level: ' . $level);
        }

        if ($this->shouldLog($level) === false) {
            return;
        }

        $formattedMessage = $this->formatter->format($level, $message, $context);
        $this->writer->write($formattedMessage);
    }

    protected function shouldLog(string $level): bool
    {
        return self::LEVELS[$level] <= self::LEVELS[$this->logLevel];
    }
}
