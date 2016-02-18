<?php

namespace Graze\DataFile\Test\Unit\Helper;

use Graze\DataFile\Test\Helper\FakeOptionalLogger;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class OptionalLoggerTraitTest
{
    /**
     * @var FakeOptionalLogger
     */
    private $logger;

    public function setUp()
    {
        $this->logger = new FakeOptionalLogger();
    }

    public function testCallingLogWithNoLoggerSetWillDoNothing()
    {
        $this->logger->doLog("Some text will not throw an exception or anything");
    }

    public function testCallingLogWithALoggerSetWillCallTheLogger()
    {
        $logger = m::mock(LoggerInterface::class);
        $this->logger->setLogger($logger);

        $logger->shouldReceive('log')
               ->with(LogLevel::INFO, 'some text', [])
               ->once();

        $this->logger->doLog('some text');
    }
}
