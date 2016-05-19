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
use Graze\DataFile\Format\Formatter\JsonFormatter;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Format\JsonFormatInterface;
use Graze\DataFile\Test\TestCase;

class JsonFormatterTest extends TestCase
{
    /**
     * @dataProvider formatTestData
     *
     * @param JsonFormatInterface $format
     * @param array               $row
     * @param string              $expected
     * @param string              $start
     * @param string              $separator
     * @param string              $end
     */
    public function testFormat(JsonFormatInterface $format, array $row, $expected, $start, $separator, $end)
    {
        $formatter = new JsonFormatter($format);

        static::assertEquals($expected, $formatter->format($row));
        static::assertEquals($start, $formatter->getInitialBlock());
        static::assertEquals($separator, $formatter->getRowSeparator());
        static::assertEquals($end, $formatter->getClosingBlock());
    }

    /**
     * @return array
     */
    public function formatTestData()
    {
        $date = new DateTime();
        return [
            [
                new JsonFormat(),
                [
                    'key'    => 'value',
                    'int'    => 1,
                    'float'  => 2.5,
                    'bool'   => true,
                    'null'   => null,
                    'array'  => ['a', 'b', 'c'],
                    'object' => (object) ['a' => 1],
                    'date'   => $date,
                ],
                '{"key":"value","int":1,"float":2.5,"bool":true,"null":null,"array":["a","b","c"],"object":{"a":1},"date":"' . $date->format('Y-m-d H:i:s') . '"}',
                '[',
                ",\n",
                "]\n",
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_ENCODE_OPTIONS => JSON_PRETTY_PRINT,
                    JsonFormat::OPTION_FILE_TYPE      => JsonFormat::JSON_FILE_TYPE_SINGLE_BLOCK,
                ]),
                [
                    'key'    => 'value',
                    'int'    => 1,
                    'float'  => 2.5,
                    'bool'   => true,
                    'null'   => null,
                    'array'  => ['a', 'b', 'c'],
                    'object' => (object) ['a' => 1],
                    'date'   => $date,
                ],
                <<<JSON
{
    "key": "value",
    "int": 1,
    "float": 2.5,
    "bool": true,
    "null": null,
    "array": [
        "a",
        "b",
        "c"
    ],
    "object": {
        "a": 1
    },
    "date": "{$date->format('Y-m-d H:i:s')}"
}
JSON
                ,
                '[',
                ",\n",
                "]\n",
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
                ]),
                [
                    'key'    => 'value',
                    'int'    => 1,
                    'float'  => 2.5,
                    'bool'   => true,
                    'null'   => null,
                    'array'  => ['a', 'b', 'c'],
                    'object' => (object) ['a' => 1],
                    'date'   => $date,
                ],
                '{"key":"value","int":1,"float":2.5,"bool":true,"null":null,"array":["a","b","c"],"object":{"a":1},"date":"' . $date->format('Y-m-d H:i:s') . '"}',
                '',
                "\n",
                '',
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE      => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
                    JsonFormat::OPTION_ENCODE_OPTIONS => JSON_PRETTY_PRINT,
                ]),
                [
                    'key'    => 'value',
                    'int'    => 1,
                    'float'  => 2.5,
                    'bool'   => true,
                    'null'   => null,
                    'array'  => ['a', 'b', 'c'],
                    'object' => (object) ['a' => 1],
                    'date'   => $date,
                ],
                '{"key":"value","int":1,"float":2.5,"bool":true,"null":null,"array":["a","b","c"],"object":{"a":1},"date":"' . $date->format('Y-m-d H:i:s') . '"}',
                '',
                "\n",
                '',
            ],
        ];
    }
}
