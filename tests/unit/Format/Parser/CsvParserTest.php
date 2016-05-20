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
use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Format\Parser\CsvParser;
use Graze\DataFile\Test\Helper\CreateStreamTrait;
use Graze\DataFile\Test\TestCase;
use Mockery as m;
use RuntimeException;

class CsvParserTest extends TestCase
{
    use CreateStreamTrait;

    /**
     * @dataProvider parseLineData
     *
     * @param CsvFormatInterface $format
     * @param string             $line
     * @param array              $expected
     */
    public function testParse(CsvFormatInterface $format, $line, array $expected)
    {
        $parser = new CsvParser($format);

        $iterator = $parser->parse($this->createStream($line));
        $actual = iterator_to_array($iterator);
        $actual = array_values(array_map('iterator_to_array', $actual));

        static::assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function parseLineData()
    {
        return [
            [
                new CsvFormat([CsvFormat::OPTION_HEADER_ROW => 0]),
                '"a","b",c,1,true,0.2,false,", enclosed","\\n \\r stuff",\\N',
                [['a', 'b', 'c', '1', 'true', '0.2', 'false', ', enclosed', "n r stuff", null]],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DELIMITER       => '|',
                    CsvFormat::OPTION_ESCAPE          => '~',
                    CsvFormat::OPTION_QUOTE_CHARACTER => '`',
                    CsvFormat::OPTION_NULL_OUTPUT     => 'null',
                    CsvFormat::OPTION_HEADER_ROW      => 0,
                    CsvFormat::OPTION_DOUBLE_QUOTE    => true,
                ]),
                '`string`|`other,thing`|some stuff|escaped ~\\n|``` all the `` quotes `|null',
                [['string', 'other,thing', 'some stuff', 'escaped \n', '` all the ` quotes ', null]],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_QUOTE_CHARACTER => "\\",
                    CsvFormat::OPTION_DELIMITER       => '|',
                    CsvFormat::OPTION_ESCAPE          => "\\",
                    CsvFormat::OPTION_HEADER_ROW      => 0,
                ]),
                'a|b|c|d\\|e',
                [['a', 'b', 'c', 'd|e']],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW   => 1,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                file_get_contents(__DIR__ . '/../../../fixtures/csv_file.csv'),
                [
                    [
                        'id'     => '0',
                        'name'   => 'my name',
                        'things' => 'i like " quotes',
                        'stuff'  => 'question?',
                    ],
                    [
                        'id'     => '1',
                        'name'   => 'your name',
                        'things' => 'potatoes! ' . "\n" . 'and stuff',
                        'stuff'  => 'think',
                    ],
                    [
                        'id'     => '2',
                        'name'   => 'your , nice',
                        'things' => 'fish"',
                        'stuff'  => '","',
                    ],
                ],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DATA_START   => 1,
                    CsvFormat::OPTION_LIMIT        => 2,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                file_get_contents(__DIR__ . '/../../../fixtures/csv_file.csv'),
                [
                    ['id', 'name', 'things', 'stuff'],
                    ['0', 'my name', 'i like " quotes', 'question?'],
                ],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DATA_START   => 4,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                file_get_contents(__DIR__ . '/../../../fixtures/csv_file.csv'),
                [
                    ['2', 'your , nice', 'fish"', '","'],
                ],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW   => 1,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                "\n" . '"1","2","3","4","5"',
                [
                    ['1', '2', '3', '4', '5'],
                ],
            ],
        ];
    }

    /**
     * @dataProvider parseFailureData
     *
     * @param CsvFormatInterface $format
     * @param string             $line
     * @param string             $expected
     */
    public function testParseFailure(CsvFormatInterface $format, $line, $expected)
    {
        $parser = new CsvParser($format);

        $iterator = $parser->parse($this->createStream($line));

        static::expectException($expected);
        $actual = iterator_to_array($iterator);
        $actual = array_values(array_map('iterator_to_array', $actual));

        static::assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function parseFailureData()
    {
        return [
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADER_ROW   => 1,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                '"id","fish","cake"' . "\n" . '"1","name","thing","too many"',
                RuntimeException::class,
            ],
        ];
    }
}
