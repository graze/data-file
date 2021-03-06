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
use Graze\DataFile\Format\Formatter\CsvFormatter;
use Graze\DataFile\Format\Formatter\FormatterInterface;
use Graze\DataFile\IO\StreamWriter;
use Graze\DataFile\Test\TestCase;
use Mockery as m;

class StreamWriterTest extends TestCase
{
    /**
     * @var resource
     */
    private $stream;

    public function setUp()
    {
        $this->stream = fopen('php://temp', 'r+');
    }

    /**
     * @return string
     */
    private function getContents()
    {
        return stream_get_contents($this->stream);
    }

    public function testInsertArray()
    {
        $data = [
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ];

        $writer = new StreamWriter($this->stream, new CsvFormatter(new CsvFormat()));

        $writer->insertAll($data);

        $expected = <<<CSV
"a","b","c","d"
"e","f","g","h"
CSV;

        fseek($this->stream, 0, SEEK_SET);
        static::assertEquals($expected, $this->getContents());
    }

    public function testInsertIterator()
    {
        $data = new ArrayIterator([
            ['a', 'b', 'c', 'd'],
            ['e', 'f', 'g', 'h'],
        ]);

        $writer = new StreamWriter($this->stream, new CsvFormatter(new CsvFormat()));

        $writer->insertAll($data);

        $expected = <<<CSV
"a","b","c","d"
"e","f","g","h"
CSV;

        fseek($this->stream, 0, SEEK_SET);
        static::assertEquals($expected, $this->getContents());
    }

    public function testInsertRow()
    {
        $writer = new StreamWriter($this->stream, new CsvFormatter(new CsvFormat()));

        $writer->insert(['a', 'b', 'c', 'd']);
        $writer->insert(['e', 'f', 'g', 'h']);

        $expected = <<<CSV
"a","b","c","d"
"e","f","g","h"
CSV;

        fseek($this->stream, 0, SEEK_SET);
        static::assertEquals($expected, $this->getContents());
    }

    public function testFormatterBlocks()
    {
        $formatter = m::mock(FormatterInterface::class);

        $writer = new StreamWriter($this->stream, $formatter);

        $formatter->shouldReceive('getInitialBlock')
                  ->andReturn('--init--');
        $formatter->shouldReceive('getClosingBlock')
                  ->andReturn('--end--');
        $formatter->shouldReceive('format')
                  ->with(['a', 'b', 'c', 'd'])
                  ->andReturn('"a","b","c","d"');
        $formatter->shouldReceive('format')
                  ->with(['e', 'f', 'g', 'h'])
                  ->andReturn('"e","f","g","h"');
        $formatter->shouldReceive('format')
                  ->with(['i', 'j', 'k', 'l'])
                  ->andReturn('"i","j","k","l"');
        $formatter->shouldReceive('getRowSeparator')
                  ->andReturn("EOL");

        $writer->insert(['a', 'b', 'c', 'd']);
        $writer->insertAll([
            ['e', 'f', 'g', 'h'],
            ['i', 'j', 'k', 'l'],
        ]);

        $expected = <<<CSV
--init--"a","b","c","d"EOL"e","f","g","h"EOL"i","j","k","l"--end--
CSV;

        fseek($this->stream, 0, SEEK_SET);
        static::assertEquals($expected, $this->getContents());

        $formatter->shouldReceive('format')
                  ->with(['m', 'n', 'o', 'p'])
                  ->andReturn('"m","n","o","p"');

        $writer->insert(['m', 'n', 'o', 'p']);

        $expected = <<<CSV
--init--"a","b","c","d"EOL"e","f","g","h"EOL"i","j","k","l"EOL"m","n","o","p"--end--
CSV;

        fseek($this->stream, 0, SEEK_SET);
        static::assertEquals($expected, $this->getContents());
    }
}
