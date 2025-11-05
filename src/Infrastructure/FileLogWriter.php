<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Infrastructure;

use Mafin\SimpleLogger\Contract\LogWriterInterface;
use RuntimeException;

final readonly class FileLogWriter implements LogWriterInterface
{
    public function __construct(
        private string $logFile,
    ) {
    }

    /**
     * Write a log message to file.
     *
     * Creates the directory if it doesn't exist and writes the message
     * to the log file with exclusive locking for thread safety.
     *
     * @throws RuntimeException if directory creation or file writing fails
     */
    public function write(string $message): void
    {
        $directory = dirname($this->logFile);

        if (is_dir($directory) === false) {
            set_error_handler(self::createErrorHandler());

            try {
                $created = mkdir($directory, 0755, true);

                if ($created === false && is_dir($directory) === false) {
                    throw new RuntimeException('Failed to create log directory: ' . $directory);
                }
            } finally {
                restore_error_handler();
            }
        }

        set_error_handler(self::createErrorHandler());

        try {
            $result = file_put_contents($this->logFile, $message, FILE_APPEND | LOCK_EX);

            if ($result === false) {
                throw new RuntimeException('Failed to write log to file: ' . $this->logFile);
            }
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Create error handler that converts PHP errors to exceptions.
     *
     * @return callable(int, string): never
     */
    private static function createErrorHandler(): callable
    {
        return static function (int $errno, string $errstr): never {
            throw new RuntimeException($errstr, $errno);
        };
    }
}
