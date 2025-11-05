<?php

declare(strict_types=1);

namespace Mafin\SimpleLogger\Infrastructure;

use Mafin\SimpleLogger\Contract\LogFormatterInterface;
use Stringable;

final class DefaultLogFormatter implements LogFormatterInterface
{
    public function format(string $level, string|Stringable $message, array $context = []): string
    {
        $message = (string) $message;
        $message = $this->interpolate($message, $context);
        $timestamp = date('Y-m-d H:i:s');

        return "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param array<string, mixed> $context
     */
    private function interpolate(string $message, array $context = []): string
    {
        if ($context === []) {
            return $message;
        }

        $replace = [];
        foreach ($context as $key => $val) {
            $placeholder = "{{$key}}";

            if (!str_contains($message, $placeholder)) {
                continue;
            }

            $replace[$placeholder] = $this->stringify($val);
        }

        return strtr($message, $replace);
    }

    private function stringify(mixed $value): string
    {
        if ($value === null) {
            return 'null';
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if ($value instanceof Stringable) {
            return (string) $value;
        }

        if (is_array($value)) {
            return json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }

        if (is_object($value)) {
            return sprintf('[object %s]', $value::class);
        }

        return '[unknown type]';
    }
}
