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

namespace Graze\DataFile\Test\Unit\IO;

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\Formatter\FormatterFactoryInterface;
use Graze\DataFile\Format\Formatter\FormatterInterface;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\IO\FileWriter;
use Graze\DataFile\IO\WriterInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\NodeStreamInterface;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;

class FileWriterTest extends TestCase
{
    public function testNodeStreamFileWillGetAStream()
    {
        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        $format = m::mock(CsvFormat::class)
                   ->makePartial();

        $writer = new FileWriter($file, $format);

        static::assertInstanceOf(WriterInterface::class, $writer);
    }

    public function testFileNodeWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);

        static::expectException(InvalidArgumentException::class);
        new FileWriter($file);
    }

    public function testNodeWithNoFormatAndNoFormatSpecifiedWillThrowAnException()
    {
        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        static::expectException(InvalidArgumentException::class);
        new FileWriter($file);
    }

    public function testNodeWithFormatWillUseThatFormat()
    {
        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class, FormatAwareInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        $format = m::mock(CsvFormat::class)
                   ->makePartial();

        $file->shouldReceive('getFormat')
             ->andReturn($format);

        $writer = new FileWriter($file);

        static::assertInstanceOf(WriterInterface::class, $writer);
    }

    public function testProvidingAParserFactoryWillUseTheFactory()
    {
        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        $format = m::mock(JsonFormat::class)
                   ->makePartial();
        $factory = m::mock(FormatterFactoryInterface::class);
        $formatter = m::mock(FormatterInterface::class);
        $factory->shouldReceive('getFormatter')
                ->with($format)
                ->andReturn($formatter);

        $writer = new FileWriter($file, $format, $factory);

        static::assertInstanceOf(WriterInterface::class, $writer);
    }

    /**
     * @return resource
     */
    private function getStream()
    {
        return fopen('php://temp', 'c+b');
    }

    public function testInsertAll()
    {
        $data = [
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ];

        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        $format = m::mock(CsvFormat::class)->makePartial();
        $formatter = m::mock(FormatterInterface::class);
        $factory = m::mock(FormatterFactoryInterface::class);
        $factory->shouldReceive('getFormatter')
                ->with($format)
                ->andReturn($formatter);
        $writer = new FileWriter($file, $format, $factory);

        $formatter->shouldReceive('format')
                  ->with(['a', 'b', 'c', 'd'])
                  ->andReturn('first line');
        $formatter->shouldReceive('format')
                  ->with(['e', 'f', 'g', 'h'])
                  ->andReturn('second line');
        $formatter->shouldReceive('getInitialBlock')
                  ->andReturn('initial' . "\n");
        $formatter->shouldReceive('getClosingBlock')
                  ->andReturn("\n" . 'close');
        $formatter->shouldReceive('getRowSeparator')
                  ->andReturn("\n");

        $expected = <<<CSV
initial
first line
second line
close
CSV;

        $writer->insertAll($data);

        fseek($stream, 0, SEEK_SET);
        static::assertEquals($expected, stream_get_contents($stream));
    }

    public function testInsertOne()
    {
        $stream = $this->getStream();

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('c+b')
             ->andReturn($stream);

        $format = m::mock(CsvFormat::class)->makePartial();
        $formatter = m::mock(FormatterInterface::class);
        $factory = m::mock(FormatterFactoryInterface::class);
        $factory->shouldReceive('getFormatter')
                ->with($format)
                ->andReturn($formatter);
        $writer = new FileWriter($file, $format, $factory);

        $formatter->shouldReceive('format')
                  ->with(['a', 'b', 'c', 'd'])
                  ->andReturn('first line');
        $formatter->shouldReceive('format')
                  ->with(['e', 'f', 'g', 'h'])
                  ->andReturn('second line');
        $formatter->shouldReceive('getInitialBlock')
                  ->andReturn('initial' . "\n");
        $formatter->shouldReceive('getClosingBlock')
                  ->andReturn("\n" . 'close');
        $formatter->shouldReceive('getRowSeparator')
                  ->andReturn("\n");

        $expected = <<<CSV
initial
first line
second line
close
CSV;

        $writer->insert(['a', 'b', 'c', 'd']);
        $writer->insert(['e', 'f', 'g', 'h']);

        fseek($stream, 0, SEEK_SET);
        static::assertEquals($expected, stream_get_contents($stream));
    }
}
