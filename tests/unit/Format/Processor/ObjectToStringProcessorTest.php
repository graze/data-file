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

use Graze\DataFile\Format\Processor\ObjectToStringProcessor;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;

class ObjectToStringProcessorTest extends TestCase
{
    public function testObjectWithStringMagicMethod()
    {
        $object = m::mock();
        $object->shouldReceive('__toString')
               ->andReturn('some string');

        $processor = new ObjectToStringProcessor();

        static::assertEquals(['some string'], $processor->process([$object]));
    }

    public function testObjectWithNoToStringWillThrowException()
    {
        $object = new \stdClass();

        $processor = new ObjectToStringProcessor();

        static::expectException(InvalidArgumentException::class);

        $processor->process([$object]);
    }

    public function testNonObjectsWillNotBeModified()
    {
        $processor = new ObjectToStringProcessor();

        static::assertEquals(['text', 1, 2.4, false, null], $processor->process(['text', 1, 2.4, false, null]));
    }

    public function testInvoke()
    {
        $object = m::mock();
        $object->shouldReceive('__toString')
               ->andReturn('some string');

        $processor = new ObjectToStringProcessor();

        static::assertEquals(['some string'], $processor([$object]));
    }
}
