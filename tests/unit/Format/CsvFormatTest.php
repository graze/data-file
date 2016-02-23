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

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Test\TestCase;

class CsvFormatTest extends TestCase
{
    public function testImplementsInterface()
    {
        $definition = new CsvFormat();

        static::assertInstanceOf('Graze\DataFile\Format\CsvFormatInterface', $definition);
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->hasQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($definition->hasHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvFormat([
            'delimiter'      => "\t",
            'quoteCharacter' => '',
            'nullOutput'     => '',
            'includeHeaders' => false,
            'lineTerminator' => "----",
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->hasQuotes(), "Quoting should be off");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($definition->hasHeaders(), "Headers should be off");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
    }

    public function testSettingProperties()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->hasQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($definition->hasHeaders(), "Headers should be on by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");

        static::assertSame($definition, $definition->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($definition, $definition->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->hasQuotes(), "Quoting should be off");
        static::assertSame($definition, $definition->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($definition, $definition->setHeaders(false), "setIncludeHeaders should be fluent");
        static::assertFalse($definition->hasHeaders(), "Headers should be off");
        static::assertSame($definition, $definition->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
    }
}
