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

namespace Graze\DataFile\Test\Unit\Format\Formatter;

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Formatter\CsvFormatter;
use Graze\DataFile\Format\Formatter\FormatterFactory;
use Graze\DataFile\Format\Formatter\FormatterFactoryInterface;
use Graze\DataFile\Format\Formatter\JsonFormatter;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;

class FormatterFactoryTest extends TestCase
{
    /**
     * @var FormatterFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new FormatterFactory();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FormatterFactoryInterface::class, $this->factory);
    }

    public function testCsvFormatter()
    {
        /** @var CsvFormat $csvFormat */
        $csvFormat = m::mock(CsvFormat::class)->makePartial();

        $formatter = $this->factory->getFormatter($csvFormat);

        static::assertInstanceOf(CsvFormatter::class, $formatter);
    }

    public function testACsvFormatPretendingToBeCsvWillThrowAnException()
    {
        /** @var CsvFormat|MockInterface $csvFormat */
        $csvFormat = m::mock(FormatInterface::class);
        $csvFormat->shouldReceive('getType')
                  ->andReturn('csv');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getFormatter($csvFormat);
    }

    public function testJsonFormatter()
    {
        /** @var JsonFormat|MockInterface $jsonFormat */
        $jsonFormat = m::mock(JsonFormat::class);
        $jsonFormat->shouldReceive('getType')
                   ->andReturn('json');
        $jsonFormat->shouldReceive('isSingleBlock')
                   ->andReturn(false);
        $jsonFormat->shouldReceive('getJsonEncodeOptions')
                   ->andReturn(0);
        $jsonFormat->shouldReceive('isEachLine')
                   ->andReturn(true);
        $formatter = $this->factory->getFormatter($jsonFormat);

        static::assertInstanceOf(JsonFormatter::class, $formatter);
    }

    public function testAJsonFormatPretendingToBeCsvWillThrowAnException()
    {
        /** @var CsvFormat|MockInterface $csvFormat */
        $csvFormat = m::mock(FormatInterface::class);
        $csvFormat->shouldReceive('getType')
                  ->andReturn('json');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getFormatter($csvFormat);
    }

    public function testGetFormatterWithUnknownTypeWillThrowException()
    {
        /** @var FormatInterface $format */
        $format = m::mock(FormatInterface::class);
        $format->shouldReceive('getType')
               ->andReturn('random');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getFormatter($format);
    }
}
