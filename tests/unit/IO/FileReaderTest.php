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

use ArrayIterator;
use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Format\Parser\ParserInterface;
use Graze\DataFile\IO\FileReader;
use Graze\DataFile\IO\ReaderInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\NodeStreamInterface;
use Graze\DataFile\Test\Helper\CreateStreamTrait;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Iterator;
use Mockery as m;
use Psr\Http\Message\StreamInterface;

class FileReaderTest extends TestCase
{
    use CreateStreamTrait;

    /**
     * @param resource        $stream
     * @param ParserInterface $parser
     *
     * @return FileReader
     */
    private function buildFactoryReader($stream, ParserInterface $parser)
    {
        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('r')
             ->andReturn($stream);

        $format = m::mock(JsonFormat::class)->makePartial();

        $factory = m::mock(ParserFactoryInterface::class);
        $factory->shouldReceive('getParser')
                ->with($format)
                ->andReturn($parser);

        return new FileReader($file, $format, $factory);
    }

    public function testNodeStreamFileWillGetAStream()
    {
        $stream = m::mock(StreamInterface::class);

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('r')
             ->andReturn($stream);

        $format = new CsvFormat();
        $reader = new FileReader($file, $format);

        static::assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testFileNodeWillGetAReadStream()
    {
        $stream = fopen('php://memory', 'r');

        try {
            $file = m::mock(FileNodeInterface::class);
            $file->shouldReceive('readStream')
                 ->andReturn($stream);

            $format = new CsvFormat();

            $reader = new FileReader($file, $format);

            static::assertInstanceOf(ReaderInterface::class, $reader);
        } finally {
            fclose($stream);
        }
    }

    public function testNodeWithNoFormatAndNoFormatSpecifiedWillThrowAnException()
    {
        $stream = fopen('php://memory', 'r');

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class);
        $file->shouldReceive('getStream')
             ->with('r')
             ->andReturn($stream);

        static::expectException(InvalidArgumentException::class);
        new FileReader($file);
    }

    public function testNodeWithFormatWillUseThatFormat()
    {
        $stream = fopen('php://memory', 'r');

        $file = m::mock(FileNodeInterface::class, NodeStreamInterface::class, FormatAwareInterface::class);
        $file->shouldReceive('getStream')
             ->with('r')
             ->andReturn($stream);

        $format = new JsonFormat(['fileType' => JsonFormat::JSON_FILE_TYPE_EACH_LINE]);

        $file->shouldReceive('getFormat')
             ->andReturn($format);

        $reader = new FileReader($file);

        static::assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testProvidingAParserFactoryWillUseTheFactory()
    {
        $stream = fopen('php://memory', 'r');
        $iterator = m::mock(Iterator::class);
        $parser = m::mock(ParserInterface::class);
        $parser->shouldReceive('parse')
               ->andReturn($iterator);

        $reader = $this->buildFactoryReader($stream, $parser);

        static::assertInstanceOf(ReaderInterface::class, $reader);
    }

    public function testFetch()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $stream = $this->createStream('some text in a stream');

        $parser = m::mock(ParserInterface::class);
        $parser->shouldReceive('parse')
               ->with($stream)
               ->andReturn($iterator);

        $reader = $this->buildFactoryReader($stream, $parser);

        $actual = $reader->fetch();
        static::assertSame($reader, $actual);
        static::assertEquals(['some', 'text'], iterator_to_array($actual));
    }

    public function testFetchAll()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $stream = $this->createStream('some text in a stream');

        $parser = m::mock(ParserInterface::class);
        $parser->shouldReceive('parse')
               ->with($stream)
               ->andReturn($iterator);

        $reader = $this->buildFactoryReader($stream, $parser);

        $actual = $reader->fetchAll();
        static::assertEquals(['some', 'text'], $actual);
    }
}
