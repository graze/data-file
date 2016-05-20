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
use Graze\DataFile\Format\Parser\ParserInterface;
use Graze\DataFile\IO\StreamReader;
use Graze\DataFile\Test\Helper\CreateStreamTrait;
use Graze\DataFile\Test\TestCase;
use Iterator;
use Mockery as m;

class StreamReaderTest extends TestCase
{
    use CreateStreamTrait;

    /**
     * @param string   $string
     * @param Iterator $iterator
     *
     * @return StreamReader
     */
    private function buildReader($string, Iterator $iterator)
    {
        $stream = $this->createStream($string);
        /** @var ParserInterface $parser */
        $parser = m::mock(ParserInterface::class);

        $parser->shouldReceive('parse')
               ->with($stream)
               ->andReturn($iterator);

        return new StreamReader($stream, $parser);
    }

    public function testFetchWithNoCallable()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $reader = $this->buildReader("some text in a stream", $iterator);

        $actual = $reader->fetch();
        static::assertSame($iterator, $actual);
    }

    public function testFetchAllWithNoCallable()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $reader = $this->buildReader("some text in a stream", $iterator);

        $actual = $reader->fetchAll();
        static::assertEquals(['some', 'text'], $actual);
    }

    public function testFetchWithCallable()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $reader = $this->buildReader("some text in a stream", $iterator);

        $actual = $reader->fetch(function ($item) {
            return $item == 'some';
        });
        static::assertNotSame($iterator, $actual);

        $result = iterator_to_array($actual);
        static::assertEquals(['some'], $result);
    }

    public function testFetchAllWithCallable()
    {
        $iterator = new ArrayIterator(['some', 'text']);
        $reader = $this->buildReader("some text in a stream", $iterator);

        $actual = $reader->fetchAll(function ($item) {
            return $item == 'text';
        });
        static::assertEquals(['text'], array_values($actual));
    }
}
