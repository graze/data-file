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

use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Format\JsonFormatInterface;
use Graze\DataFile\Format\Parser\JsonParser;
use Graze\DataFile\Test\Helper\CreateStreamTrait;
use Graze\DataFile\Test\TestCase;
use RuntimeException;

class JsonParserTest extends TestCase
{
    use CreateStreamTrait;

    /**
     * @dataProvider parseJsonData
     *
     * @param JsonFormatInterface $format
     * @param string              $json
     * @param array               $expected
     */
    public function testParse(JsonFormatInterface $format, $json, array $expected)
    {
        $parser = new JsonParser($format);

        $iterator = $parser->parse($this->createStream($json));
        $actual = iterator_to_array($iterator);

        static::assertEquals($expected, $actual);
    }

    /**
     * @return array
     */
    public function parseJsonData()
    {
        return [
            [
                new JsonFormat(),
                <<<JSON
[
    {
        "name": "value",
        "number": 1.2,
        "bool": true,
        "null": null,
        "thing": {
            "hi": "there"
        },
        "array": [
            "a",
            "1"
        ]
    },
    {
        "other": "stuff"
    }
]
JSON
                ,
                [
                    (object) [
                        'name'   => 'value',
                        'number' => 1.2,
                        'bool'   => true,
                        'null'   => null,
                        'thing'  => (object) [
                            'hi' => 'there',
                        ],
                        'array'  => ['a', '1'],
                    ],
                    (object) [
                        'other' => 'stuff',
                    ],
                ],
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_DECODE_ASSOC => true,
                ]),
                <<<JSON
[
    {
        "name": "value\\nstuff",
        "number": 1.2,
        "bool": true,
        "null": null,
        "thing": {
            "hi": "there"
        },
        "array": [
            "a",
            "1"
        ]
    },
    {
        "other": "stuff"
    }
]
JSON
                ,
                [
                    [
                        'name'   => "value\nstuff",
                        'number' => 1.2,
                        'bool'   => true,
                        'null'   => null,
                        'thing'  => [
                            'hi' => 'there',
                        ],
                        'array'  => ['a', '1'],
                    ],
                    [
                        'other' => 'stuff',
                    ],
                ],
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
                ]),
                <<<JSON
{"name": "value\\nline","number": 1.2,"bool": true,"null": null,"thing": {"hi": "there"},"array": ["a","1"]}
{"other": "stuff"}
JSON
                ,
                [
                    (object) [
                        'name'   => "value\nline",
                        'number' => 1.2,
                        'bool'   => true,
                        'null'   => null,
                        'thing'  => (object) [
                            'hi' => 'there',
                        ],
                        'array'  => ['a', '1'],
                    ],
                    (object) [
                        'other' => 'stuff',
                    ],
                ],
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE          => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
                    JsonFormat::OPTION_IGNORE_BLANK_LINES => true,
                ]),
                <<<JSON

{"name": "value\\nline","number": 1.2,"bool": true,"null": null,"thing": {"hi": "there"},"array": ["a","1"]}



{"other": "stuff"}

JSON
                ,
                [
                    (object) [
                        'name'   => "value\nline",
                        'number' => 1.2,
                        'bool'   => true,
                        'null'   => null,
                        'thing'  => (object) [
                            'hi' => 'there',
                        ],
                        'array'  => ['a', '1'],
                    ],
                    (object) [
                        'other' => 'stuff',
                    ],
                ],
            ],
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE          => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
                    JsonFormat::OPTION_IGNORE_BLANK_LINES => false,
                ]),
                <<<JSON

{"name": "value\\nline","number": 1.2,"bool": true,"null": null,"thing": {"hi": "there"},"array": ["a","1"]}



{"other": "stuff"}

JSON
                ,
                [
                    null,
                    (object) [
                        'name'   => "value\nline",
                        'number' => 1.2,
                        'bool'   => true,
                        'null'   => null,
                        'thing'  => (object) [
                            'hi' => 'there',
                        ],
                        'array'  => ['a', '1'],
                    ],
                    null,
                    null,
                    null,
                    (object) [
                        'other' => 'stuff',
                    ],
                    null,
                ],
            ],
        ];
    }

    /**
     * @dataProvider parseFailuresData
     *
     * @param JsonFormatInterface $format
     * @param string              $json
     * @param string              $exception
     * @param string|null         $regex
     */
    public function testParseFailures(JsonFormatInterface $format, $json, $exception, $regex = null)
    {
        $parser = new JsonParser($format);

        static::expectException($exception);
        if ($regex) {
            static::expectExceptionMessageRegExp($regex);
        }
        $iterator = $parser->parse($this->createStream($json));

        iterator_to_array($iterator);
    }

    /**
     * @return array
     */
    public function parseFailuresData()
    {
        return [
            [
                new JsonFormat([
                    JsonFormat::OPTION_FILE_TYPE => JsonFormat::JSON_FILE_TYPE_SINGLE_BLOCK,
                ]),
                '{"not","an","array"}',
                RuntimeException::class,
                "/Expecting a json array to parse, unknown format detected/",
            ],
        ];
    }
}
