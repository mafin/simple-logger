<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Mafin\SimpleLogger\Logger;
use Psr\Log\LogLevel;

$logger = new Logger('test.log', LogLevel::DEBUG);
$logger->info('This is an info log message.');
$logger->error('This is an error log message.');

echo file_get_contents('test.log') . PHP_EOL;
echo 'Done.' . PHP_EOL;
