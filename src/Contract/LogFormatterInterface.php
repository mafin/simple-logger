<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Contract;

use Stringable;

interface LogFormatterInterface
{
    /**
     * Format a log message with level and context.
     *
     * @param array<string, mixed> $context
     */
    public function format(string $level, string|Stringable $message, array $context = []): string;
}
