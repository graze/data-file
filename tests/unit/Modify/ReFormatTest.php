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

namespace Graze\DataFile\Test\Unit\Modify;

use ArrayIterator;
use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Formatter\FormatterFactoryInterface;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Format\Parser\ParserInterface;
use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\IO\FileReader;
use Graze\DataFile\IO\FileWriter;
use Graze\DataFile\Modify\FileModifierInterface;
use Graze\DataFile\Modify\ReFormat;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Node\LocalFileNodeInterface;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;

class ReFormatTest extends TestCase
{
    /** @var ParserFactoryInterface|MockInterface */
    private $parserFactory;
    /** @var FormatterFactoryInterface|MockInterface */
    private $formatterFactory;
    /** @var ReFormat */
    private $reFormatter;
    /** @var BuilderInterface|MockInterface */
    private $builder;

    public function setUp()
    {
        parent::setUp();

        $this->builder = m::mock(BuilderInterface::class);
        $this->parserFactory = m::mock(ParserFactoryInterface::class);
        $this->formatterFactory = m::mock(FormatterFactoryInterface::class);
        $this->reFormatter = new ReFormat($this->formatterFactory, $this->parserFactory, $this->builder);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FileModifierInterface::class, $this->reFormatter);
    }

    public function testCanModifyAcceptsFileNodeInterfaceWithAFormat()
    {
        $file = m::mock(FileNodeInterface::class, FormatAwareInterface::class);
        $file->shouldReceive('exists')
             ->andReturn(true);

        $format = m::mock(FormatInterface::class);
        $parser = m::mock(ParserInterface::class);
        $this->parserFactory->shouldReceive('getParser')
                            ->with($format)
                            ->andReturn($parser);

        $file->shouldReceive('getFormat')
             ->andReturn($format);

        static::assertTrue($this->reFormatter->canModify($file));
    }

    public function testCanModifyDoesNotAcceptAFormatThatCanNotBeParsed()
    {
        $file = m::mock(FileNodeInterface::class, FormatAwareInterface::class);
        $file->shouldReceive('exists')
             ->andReturn(true);

        $format = m::mock(FormatInterface::class);
        $this->parserFactory->shouldReceive('getParser')
                            ->with($format)
                            ->andReturn(null);

        $file->shouldReceive('getFormat')
             ->andReturn($format);

        static::assertFalse($this->reFormatter->canModify($file));
    }

    public function testCanReFormatWithANonLocalInputFileAndOutputFormat()
    {
        $file = m::mock(FileNodeInterface::class, FormatAwareInterface::class);
        $target = m::mock(LocalFile::class);

        $this->builder->shouldReceive('build')
                      ->with(LocalFile::class, m::type('string'))
                      ->andReturn($target);

        $format = m::mock(FormatInterface::class);

        $target->shouldReceive('setFormat')
               ->with($format)
               ->andReturn($target);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $file, null, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $target, $format, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($target, $this->reFormatter->reFormat($file, $format));
    }

    public function testCanReFormatALocalInputFile()
    {
        $file = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);
        $target = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);

        $file->shouldReceive('getPath')
             ->andReturn('/tmp/file.txt');

        $file->shouldReceive('getClone')
             ->andReturn($target);
        $target->shouldReceive('setPath')
               ->with('/tmp/file-format.txt')
               ->andReturn($target);

        $format = m::mock(FormatInterface::class);

        $target->shouldReceive('setFormat')
               ->with($format)
               ->andReturn($target);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $file, null, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $target, $format, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($target, $this->reFormatter->reFormat($file, $format));
    }

    public function testCanReformatWithIntputAndOutputFiles()
    {
        $file = m::mock(FileNodeInterface::class, FormatAwareInterface::class);
        $target = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $file, null, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $target, null, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($target, $this->reFormatter->reFormat($file, null, $target));
    }

    public function testInputAndOutputFormatsArePassedToReaderAndWriter()
    {
        $input = m::mock(FileNodeInterface::class);
        $output = m::mock(FileNodeInterface::class);
        $inputFormat = m::mock(FormatInterface::class);
        $outputFormat = m::mock(FormatInterface::class);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $input, $inputFormat, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $output, $outputFormat, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($output, $this->reFormatter->reFormat($input, $outputFormat, $output, $inputFormat));
    }

    public function testModifyWithOutOutputOrFormatSpecified()
    {
        $file = m::mock(FileNodeInterface::class);

        static::expectException(InvalidArgumentException::class);

        $this->reFormatter->modify($file);
    }

    public function testModifyWithOutputFileSet()
    {
        $file = m::mock(FileNodeInterface::class, FormatAwareInterface::class);
        $target = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $file, null, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $target, null, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($target, $this->reFormatter->modify($file, ['output' => $target]));
    }

    public function testModifyWithFormatOption()
    {
        $file = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);
        $target = m::mock(LocalFileNodeInterface::class, FormatAwareInterface::class);

        $file->shouldReceive('getPath')
             ->andReturn('/tmp/file.txt');

        $file->shouldReceive('getClone')
             ->andReturn($target);
        $target->shouldReceive('setPath')
               ->with('/tmp/file-format.txt')
               ->andReturn($target);

        $format = m::mock(FormatInterface::class);

        $target->shouldReceive('setFormat')
               ->with($format)
               ->andReturn($target);

        $reader = m::mock(FileReader::class);
        $this->builder->shouldReceive('build')
                      ->with(FileReader::class, $file, null, $this->parserFactory)
                      ->andReturn($reader);
        $writer = m::mock(FileWriter::class);
        $this->builder->shouldReceive('build')
                      ->with(FileWriter::Class, $target, $format, $this->formatterFactory)
                      ->andReturn($writer);

        $iterator = new ArrayIterator(['first', 'second']);

        $reader->shouldReceive('fetch')
               ->andReturn($iterator);

        $writer->shouldReceive('insertOne')
               ->with('first')
               ->once();
        $writer->shouldReceive('insertOne')
               ->with('second')
               ->once();

        static::assertSame($target, $this->reFormatter->modify($file, ['format' => $format]));
    }
}
