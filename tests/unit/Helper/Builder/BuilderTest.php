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

namespace Graze\DataFile\Test\Unit\Helper\Builder;

use Graze\DataFile\Helper\Builder\Builder;
use Graze\DataFile\Test\Helper\Builder\FakeBuilderAware;
use Graze\DataFile\Test\Helper\Builder\FakeConstructable;
use Graze\DataFile\Test\Helper\FakeOptionalLogger;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class BuilderTest extends TestCase
{
    /** @var Builder */
    private $builder;

    public function setUp()
    {
        $this->builder = new Builder();
    }

    public function testCallingBuildWithAnInstantiatedObjectWillReturnTheObject()
    {
        $object = new \stdClass();

        static::assertSame($object, $this->builder->build($object));
    }

    public function testCallingBuildWithANonClassWillThrowAnException()
    {
        static::expectException(InvalidArgumentException::class);

        $this->builder->build('SomeRandomClassThatDoesNotExist');
    }

    public function testBuildCallSetLoggerIfTheTargetIsALoggerAwareClass()
    {
        $logger = m::mock(LoggerInterface::class);
        $this->builder->setLogger($logger);

        $logger->shouldReceive('log')
               ->with(
                   LogLevel::DEBUG,
                   Builder::class . ": Building class: {class}",
                   ['class' => FakeOptionalLogger::class]
               )
               ->once();

        /** @var FakeOptionalLogger $object */
        $object = $this->builder->build(FakeOptionalLogger::class);

        $logger->shouldReceive('log')
               ->with(LogLevel::INFO, FakeOptionalLogger::class . ': some text', [])
               ->once();
        $object->doLog('some text');
    }

    public function testBuildCallSetBuilderIfTargetIsBuilderAware()
    {
        /** @var FakeBuilderAware $object */
        $object = $this->builder->build(FakeBuilderAware::class);

        static::assertSame($this->builder, $object->getBuilder());
    }

    public function testArgumentsArePassedToConstructor()
    {
        /** @var FakeConstructable $object */
        $object = $this->builder->build(FakeConstructable::class, 'first', 'second');

        static::assertEquals('first', $object->getFirst());
        static::assertEquals('second', $object->getSecond());
        static::assertEquals(null, $object->getThird());
    }
}
