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
use Graze\DataFile\Test\TestCase;
use GuzzleHttp\Psr7\Stream;
use Mockery as m;
use Psr\Http\Message\StreamInterface;

class CsvParserTest extends TestCase
{
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
     * @param string $string
     *
     * @return StreamInterface
     */
    protected function createStream($string)
    {
        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $string);
        rewind($stream);
        return new Stream($stream);
    }

    /**
     * @return array
     */
    public function parseLineData()
    {
        return [
            [
                new CsvFormat([CsvFormat::OPTION_HEADERS => 0]),
                '"a","b",c,1,true,0.2,false,", enclosed","\\n \\r stuff",\\N',
                [['a', 'b', 'c', '1', 'true', '0.2', 'false', ', enclosed', "n r stuff", null]],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_DELIMITER       => '|',
                    CsvFormat::OPTION_ESCAPE          => '~',
                    CsvFormat::OPTION_QUOTE_CHARACTER => '`',
                    CsvFormat::OPTION_NULL_OUTPUT     => 'null',
                    CsvFormat::OPTION_HEADERS         => 0,
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
                    CsvFormat::OPTION_HEADERS         => 0,
                ]),
                'a|b|c|d\\|e',
                [['a', 'b', 'c', 'd|e']],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADERS      => 1,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                file_get_contents(__DIR__ . '/../../../fixtures/csv_file.csv'),
                [
                    ['0', 'my name', 'i like " quotes', 'question?'],
                    ['1', 'your name', 'potatoes! ' . "\n" . 'and stuff', 'think'],
                    ['2', 'your , nice', 'fish"', '","'],
                ],
            ],
            [
                new CsvFormat([
                    CsvFormat::OPTION_HEADERS      => 0,
                    CsvFormat::OPTION_LIMIT        => 2,
                    CsvFormat::OPTION_DOUBLE_QUOTE => true,
                ]),
                file_get_contents(__DIR__ . '/../../../fixtures/csv_file.csv'),
                [
                    ['id', 'name', 'things', 'stuff'],
                    ['0', 'my name', 'i like " quotes', 'question?'],
                ],
            ],
        ];
    }
}
