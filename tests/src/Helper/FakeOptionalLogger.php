<?php

namespace Graze\DataFile\Test\Helper;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class FakeOptionalLogger implements LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /**
     * @param string $text
     */
    public function doLog($text)
    {
        $this->log(LogLevel::INFO, $text);
    }
}
