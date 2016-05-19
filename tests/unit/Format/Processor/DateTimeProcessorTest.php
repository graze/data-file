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

namespace Graze\DataFile\Test\Unit\Format\Processor;

use DateTime;
use Graze\DataFile\Format\Processor\DateTimeProcessor;
use Graze\DataFile\Test\TestCase;

class DateTimeProcessorTest extends TestCase
{
    public function testDateTimeFormat()
    {
        $processor = new DateTimeProcessor();

        $date = new DateTime();

        static::assertEquals([$date->format('Y-m-d H:i:s')], $processor->process([$date]));
    }

    public function testCustomFormat()
    {
        $processor = new DateTimeProcessor('Y-m-d');

        $date = new DateTime();

        static::assertEquals([$date->format('Y-m-d')], $processor->process([$date]));
    }

    public function testIgnoreNonDateTimes()
    {
        $processor = new DateTimeProcessor('Y-m-d');

        static::assertEquals(['stirng', 1, 2.5, false, null], $processor->process(['stirng', 1, 2.5, false, null]));
    }

    public function testInvoke()
    {
        $processor = new DateTimeProcessor();

        $date = new DateTime();

        static::assertEquals([$date->format('Y-m-d H:i:s')], $processor([$date]));
    }
}
