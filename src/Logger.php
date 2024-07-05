<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;

class Logger extends AbstractLogger
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

    public function __construct(
        public readonly string $logFile,
        public readonly string $logLevel = LogLevel::DEBUG,
    ) {
    }

    /**
     * @param string $level
     */
    public function log(
        $level,
        string|Stringable $message,
        array $context = [],
    ): void {
        if (!isset(self::LEVELS[$level])) {
            throw new \Psr\Log\InvalidArgumentException('Invalid log level: ' . $level);
        }

        $message = $this->getMessageWithContext($message);

        if ($this->shouldLog($level)) {
            $timestamp = date('Y-m-d H:i:s');
            $message = $this->interpolate($message, $context);
            $logMessage = "[$timestamp] [$level] $message" . PHP_EOL;
            file_put_contents($this->logFile, $logMessage, FILE_APPEND);
        }
    }

    protected function shouldLog(string $level): bool
    {
        return self::LEVELS[$level] <= self::LEVELS[$this->logLevel];
    }

    protected function interpolate(
        string $message,
        array $context = [],
    ): string {
        $replace = array_map(
            static fn ($key, $val) => ["{{$key}}" => $val],
            array_keys($context),
            $context,
        );

        return strtr($message, $replace);
    }

    private function getMessageWithContext(string|Stringable $message): string
    {
        if ($message instanceof Stringable) {
            return $message->__toString();
        }

        return $message;
    }
}
