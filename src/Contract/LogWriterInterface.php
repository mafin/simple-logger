<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Contract;

use RuntimeException;

interface LogWriterInterface
{
    /**
     * Write a log message to the destination.
     *
     * @throws RuntimeException if writing fails
     */
    public function write(string $message): void;
}
