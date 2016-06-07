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

namespace Graze\DataFile\Test\Unit\Format\Parser;

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Formatter\CsvFormatter;
use Graze\DataFile\Format\Formatter\JsonFormatter;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Format\Parser\CsvParser;
use Graze\DataFile\Format\Parser\JsonParser;
use Graze\DataFile\Format\Parser\ParserFactory;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;

class ParserFactoryTest extends TestCase
{
    /**
     * @var ParserFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new ParserFactory();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(ParserFactoryInterface::class, $this->factory);
    }

    public function testCsvParser()
    {
        $csvFormat = m::mock(CsvFormat::class)->makePartial();

        $formatter = $this->factory->getParser($csvFormat);

        static::assertInstanceOf(CsvParser::class, $formatter);
    }

    public function testACsvFormatPretendingToBeCsvWillThrowAnException()
    {
        $csvFormat = m::mock(FormatInterface::class);
        $csvFormat->shouldReceive('getType')
                  ->andReturn('csv');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getParser($csvFormat);
    }

    public function testJsonParser()
    {
        $jsonFormat = m::mock(JsonFormat::class);
        $jsonFormat->shouldReceive('getType')
                   ->andReturn('json');
        $jsonFormat->shouldReceive('isSingleBlock')
                   ->andReturn(false);
        $jsonFormat->shouldReceive('getJsonEncodeOptions')
                   ->andReturn(0);
        $jsonFormat->shouldReceive('isEachLine')
                   ->andReturn(true);
        $formatter = $this->factory->getParser($jsonFormat);

        static::assertInstanceOf(JsonParser::class, $formatter);
    }

    public function testAJsonFormatPretendingToBeCsvWillThrowAnException()
    {
        $csvFormat = m::mock(FormatInterface::class);
        $csvFormat->shouldReceive('getType')
                  ->andReturn('json');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getParser($csvFormat);
    }

    public function testGetFormatterWithUnknownTypeWillThrowException()
    {
        $format = m::mock(FormatInterface::class);
        $format->shouldReceive('getType')
               ->andReturn('random');

        static::expectException(InvalidArgumentException::class);

        $this->factory->getParser($format);
    }
}
