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

use DateTime;
use Graze\CsvToken\Csv\Bom;
use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Format\Formatter\CsvFormatter;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use Mockery as m;

class CsvFormatterTest extends TestCase
{
    /**
     * @dataProvider getFormattingData
     *
     * @param CsvFormatInterface $format
     * @param array              $row
     * @param string             $expected
     * @param string             $message
     */
    public function testEncoding(CsvFormatInterface $format, array $row, $expected, $message = '')
    {
        $formatter = new CsvFormatter($format);
        static::assertEquals($expected, $formatter->format($row), $message);
    }

    /**
     * @return array
     */
    public function getFormattingData()
    {
        $format = new CsvFormat();

        $date = new DateTime();

        $object = m::mock();
        $object->shouldReceive('__toString')
               ->andReturn('object');

        return [
            [
                $format,
                [
                    'text',
                    1,
                    2.5,
                    null,
                    true,
                    "string wit '",
                    'string with "',
                    'string with \\',
                    'string with ,',
                    "multi line \n string",
                    $object,
                ],
                '"text","1","2.5",\\N,"1","string wit \'","string with \"","string with \\\\","string with \\,","multi line \\' . "\n" . ' string","object"',
                'basic formatting failed',
            ],
            [
                $format,
                [$date],
                sprintf('"%s"', $date->format('Y-m-d H:i:s')),
                'date formatting failed',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DELIMITER => '|',
                    CsvFormat::OPTION_NULL      => "NULL",
                    CsvFormat::OPTION_QUOTE     => "'",
                    CsvFormat::OPTION_NEW_LINE  => '---',
                    CsvFormat::OPTION_ESCAPE    => '"',
                ]),
                ['text', 1, 2.5, false, null, '|-', "'", '"'],
                "'text'|'1'|'2.5'|'0'|NULL|'\"|-'|'\"''|'\"\"'",
                'different options failed',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DELIMITER => "\t",
                    CsvFormat::OPTION_NULL      => 'null',
                    CsvFormat::OPTION_QUOTE     => '',
                    CsvFormat::OPTION_NEW_LINE  => "\n",
                ]),
                ['text', 1, 2.5, false, null, "\t a", ",", "\n"],
                "text\t1\t2.5\t0\tnull\t" . '\\' . "\t a\t,\t" . '\\' . "\n",
                'tab separated options failed',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                ['text"', 1, 2.5, false, null, "\n \r a", "\t", '""foo""'],
                '"text""","1","2.5","0",\\N,"\\' . "\n" . ' \\' . "\r" . ' a","\\' . "\t" . '","""""foo"""""',
                'double quotes failed',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_ESCAPE       => '',
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                ['text', '"test', ','],
                '"text","""test",","',
                'no escape failed',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_QUOTE        => '',
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                ['text', 'things,', '"here"'],
                'text,things\,,"here"',
                'blank quote character failed and double quote should do nothing',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW => 2,
                ]),
                ['name' => 'text', 'stuff' => 'things', 'cake' => 'pants'],
                '"name","stuff","cake"' . "\n" .
                '"text","things","pants"',
                'header row should print the array keys',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW => 3,
                    CsvFormat::OPTION_DATA_START => 6,
                ]),
                ['name' => 'text', 'stuff' => 'things', 'cake' => 'pants'],
                '"name","stuff","cake"' . "\n\n\n" .
                '"text","things","pants"',
                'having a sparse start should add spaces before and after the header row',
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW => 1,
                ]),
                ['text', 'things,', '"here"'],
                '"0","1","2"' . "\n" . '"text","things\,","\"here\""',
                'should out put numbers when no keys are provided',
            ],
        ];
    }

    public function handleObjectWithNoToStringMethod()
    {
        $object = m::mock();

        $formatter = new CsvFormatter(new CsvFormat());

        static::expectException(InvalidArgumentException::class);

        $formatter->format([$object]);
    }

    public function testInvoke()
    {
        $formatter = new CsvFormatter(new CsvFormat());

        static::assertEquals('"test","1","2","4.3"', $formatter(['test', 1, 2, 4.3]));
    }

    public function testRowSeparator()
    {
        $formatter = new CsvFormatter(new CsvFormat());

        static::assertEquals("\n", $formatter->getRowSeparator());

        $formatter = new CsvFormatter(new CsvFormat([CsvFormat::OPTION_NEW_LINE => '---']));

        static::assertEquals('---', $formatter->getRowSeparator());
    }

    public function testStartAndEndBlocksAreEmpty()
    {
        $formatter = new CsvFormatter(new CsvFormat());

        static::assertEmpty($formatter->getInitialBlock());
        static::assertEmpty($formatter->getClosingBlock());
    }

    public function testStartBlockContainsABom()
    {
        $formatter = new CsvFormatter(new CsvFormat([
            CsvFormat::OPTION_BOM => Bom::BOM_UTF16_BE,
        ]));

        static::assertEquals(Bom::BOM_UTF16_BE, $formatter->getInitialBlock());
        static::assertEmpty($formatter->getClosingBlock());
    }

    public function testStartBlockContainsSpacingWhenUsingHeaders()
    {
        $formatter = new CsvFormatter(new CsvFormat([
            CsvFormat::OPTION_HEADER_ROW => 3,
        ]));

        static::assertEquals("\n\n", $formatter->getInitialBlock());
        static::assertEmpty($formatter->getClosingBlock());

        $formatter = new CsvFormatter(new CsvFormat([
            CsvFormat::OPTION_DATA_START => 4,
            CsvFormat::OPTION_BOM        => Bom::BOM_UTF8,
        ]));

        static::assertEquals(Bom::BOM_UTF8 . "\n\n\n", $formatter->getInitialBlock());
        static::assertEmpty($formatter->getClosingBlock());
    }

    public function testFormatWithInvalidInputWillThrowAnException()
    {
        $formatter = new CsvFormatter(new CsvFormat());

        static::expectException(InvalidArgumentException::class);

        $stuff = (object) ['cake'];

        $formatter->format($stuff);
    }
}
