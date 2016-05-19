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

namespace Graze\DataFile\Test\Unit\Format;

use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Test\TestCase;

class JsonFormatTest extends TestCase
{
    public function testType()
    {
        $format = new JsonFormat();
        static::assertEquals('json', $format->getType());
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new JsonFormat();

        static::assertEquals(
            JsonFormat::JSON_FILE_TYPE_SINGLE_BLOCK,
            $definition->getJsonFileType(),
            "File Type should be single block"
        );
        static::assertEquals(0, $definition->getJsonEncodeOptions(), "Default encode options should be 0");
        static::assertEquals(0, $definition->getJsonDecodeOptions(), "Default decode options should be 0");
        static::assertFalse($definition->isJsonDecodeAssoc(), "Default decode association should be false");
        static::assertTrue($definition->isIgnoreBlankLines(), "Default ignore blank lines should be true");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new JsonFormat([
            'fileType'                            => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
            'encodeOptions'                       => JSON_PRETTY_PRINT | JSON_HEX_AMP,
            'decodeOptions'                       => JSON_BIGINT_AS_STRING,
            JsonFormat::OPTION_DECODE_ASSOC       => true,
            JsonFormat::OPTION_IGNORE_BLANK_LINES => false,
        ]);

        static::assertEquals(
            JsonFormat::JSON_FILE_TYPE_EACH_LINE,
            $definition->getJsonFileType(),
            "File Type should be set to: Each Line"
        );
        static::assertEquals(
            JSON_PRETTY_PRINT | JSON_HEX_AMP,
            $definition->getJsonEncodeOptions(),
            "Encode options should be pretty print and hex amp"
        );
        static::assertEquals(
            JSON_BIGINT_AS_STRING,
            $definition->getJsonDecodeOptions(),
            "Decode options should be BIGINT_AS_STRING"
        );
        static::assertTrue($definition->isJsonDecodeAssoc(), "Json Decode Assoc should be true");
        static::assertFalse($definition->isIgnoreBlankLines(), "Ignore Blank Lines should be false");
    }

    public function testSettingProperties()
    {
        $definition = new JsonFormat();

        static::assertEquals(
            JsonFormat::JSON_FILE_TYPE_SINGLE_BLOCK,
            $definition->getJsonFileType(),
            "File Type should be single block"
        );
        static::assertEquals(0, $definition->getJsonEncodeOptions(), "Default encode options should be 0");
        static::assertEquals(0, $definition->getJsonDecodeOptions(), "Default decode options should be 0");
        static::assertFalse($definition->isJsonDecodeAssoc(), "Default decode association should be false");
        static::assertTrue($definition->isIgnoreBlankLines(), "Default ignore blank lines should be true");

        static::assertSame(
            $definition,
            $definition->setJsonFileType(JsonFormat::JSON_FILE_TYPE_EACH_LINE),
            "setJsonFileType should be fluent"
        );
        static::assertEquals(
            JsonFormat::JSON_FILE_TYPE_EACH_LINE,
            $definition->getJsonFileType(),
            "File Type should be set to: Each Line"
        );
        static::assertSame(
            $definition,
            $definition->setJsonEncodeOptions(JSON_HEX_APOS | JSON_FORCE_OBJECT),
            "setJsonEncodeOptions should be fluent"
        );
        static::assertEquals(
            JSON_HEX_APOS | JSON_FORCE_OBJECT,
            $definition->getJsonEncodeOptions(),
            "Encode options should be: HEX_APOS and FORCE_OBJECT"
        );
        static::assertSame(
            $definition,
            $definition->setJsonDecodeOptions(JSON_BIGINT_AS_STRING),
            "setJsonDencodeOptions should be fluent"
        );
        static::assertEquals(
            JSON_BIGINT_AS_STRING,
            $definition->getJsonDecodeOptions(),
            "Decode options should be BIGINT_AS_STRING"
        );

        static::assertSame($definition, $definition->setJsonDecodeAssoc(true), "setJsonDecodeAssoc should be fluent");
        static::assertTrue($definition->isJsonDecodeAssoc(), "JsonDecodeAssoc should be true");
        static::assertSame(
            $definition,
            $definition->setIgnoreBlankLines(false),
            "setIgnoreBlankLines should be fluent"
        );
        static::assertFalse($definition->isIgnoreBlankLines(), "Ignore Blank Lines should be false");
    }
}
