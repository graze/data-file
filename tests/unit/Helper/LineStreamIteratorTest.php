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

namespace Graze\DataFile\Test\Unit\Helper;

use Graze\DataFile\Helper\LineStreamIterator;
use Graze\DataFile\Test\Helper\CreateStreamTrait;
use Graze\DataFile\Test\TestCase;

class LineStreamIteratorTest extends TestCase
{
    use CreateStreamTrait;

    /**
     * @dataProvider lineStreamData
     *
     * @param string   $string
     * @param array    $options
     * @param string[] $expected
     */
    public function testLineStream($string, array $options, array $expected)
    {
        $stream = $this->createStream($string);

        $iterator = new LineStreamIterator($stream, $options);

        static::assertEquals($expected, iterator_to_array($iterator));
    }

    /**
     * @return array
     */
    public function lineStreamData()
    {
        return [
            [
                <<<TEXT
Line 1
Line 2
Line 3

Line 5
Line 6
TEXT
                ,
                [
                    LineStreamIterator::OPTION_ENDING         => "\n",
                    LineStreamIterator::OPTION_IGNORE_BLANK   => false,
                    LineStreamIterator::OPTION_INCLUDE_ENDING => false,
                ],
                [
                    "Line 1",
                    "Line 2",
                    "Line 3",
                    "",
                    "Line 5",
                    "Line 6",
                ],
            ],
            [
                <<<TEXT
Line 1---Line 2---Line 3---Line 4
TEXT
                ,
                [
                    LineStreamIterator::OPTION_ENDING         => '---',
                    LineStreamIterator::OPTION_IGNORE_BLANK   => true,
                    LineStreamIterator::OPTION_INCLUDE_ENDING => true,
                ],
                [
                    "Line 1---",
                    "Line 2---",
                    "Line 3---",
                    "Line 4",
                ],
            ],
        ];
    }

    public function testIteratorMethods()
    {
        $stream = $this->createStream("Line 1\nLine 2");

        $iterator = new LineStreamIterator($stream);

        $iterator->rewind();
        static::assertEquals(0, $iterator->key());
        static::assertEquals("Line 1", $iterator->current());

        $iterator->next();
        static::assertEquals(1, $iterator->key());
        static::assertEquals("Line 2", $iterator->current());
    }

    public function testDefaultOptions()
    {
        $stream = $this->createStream("Test");
        $iterator = new LineStreamIterator($stream);

        static::assertEquals("\n", $iterator->getEnding());
        static::assertEquals(true, $iterator->isIgnoreBlank());
        static::assertEquals(false, $iterator->isIncludeEnding());
    }

    public function testCustomOptions()
    {
        $stream = $this->createStream("Test");
        $iterator = new LineStreamIterator($stream, [
            LineStreamIterator::OPTION_ENDING         => '---',
            LineStreamIterator::OPTION_IGNORE_BLANK   => false,
            LineStreamIterator::OPTION_INCLUDE_ENDING => true,
        ]);

        static::assertEquals("---", $iterator->getEnding());
        static::assertEquals(false, $iterator->isIgnoreBlank());
        static::assertEquals(true, $iterator->isIncludeEnding());
    }

    public function testSettingOptions()
    {
        $stream = $this->createStream("Test");
        $iterator = new LineStreamIterator($stream);

        static::assertEquals("\n", $iterator->getEnding());
        static::assertEquals(true, $iterator->isIgnoreBlank());
        static::assertEquals(false, $iterator->isIncludeEnding());

        static::assertSame($iterator, $iterator->setEnding("---"));
        static::assertEquals('---', $iterator->getEnding());
        static::assertSame($iterator, $iterator->setIgnoreBlank(false));
        static::assertFalse($iterator->isIgnoreBlank());
        static::assertSame($iterator, $iterator->setIncludeEnding(true));
        static::assertTrue($iterator->isIncludeEnding());
    }
}
