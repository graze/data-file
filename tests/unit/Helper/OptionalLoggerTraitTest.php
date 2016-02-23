<?php
/**
 * This file is part of graze/data-file
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-file/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-file
 */

namespace Graze\DataFile\Test\Unit\Helper;

use Graze\DataFile\Test\Helper\FakeOptionalLogger;
use Graze\DataFile\Test\TestCase;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class OptionalLoggerTraitTest extends TestCase
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
               ->with(LogLevel::INFO, 'Graze\DataFile\Test\Helper\FakeOptionalLogger: some text', [])
               ->once();

        $this->logger->doLog('some text');
    }
}
