<?php

namespace Graze\DataFile\Helper;

use Psr\Log\LoggerAwareTrait;

trait OptionalLoggerTrait
{
    use LoggerAwareTrait;

    /**
     * Send a log message only if we have a logger instantiated
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, __CLASS__ . ": " . $message, $context);
        }
    }
}
