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

        static::assertEquals('csv', $definition->getType());
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->hasQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertFalse($definition->hasHeaderRow(), "Headers should be on by default");
        static::assertEquals(-1, $definition->getHeaderRow(), "Header row should be -1 by default");
        static::assertEquals(1, $definition->getDataStart(), "Get data start should be 1 by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertEquals('\\', $definition->getEscapeCharacter(), "Default escape character should be '\\'");
        static::assertTrue($definition->hasEscapeCharacter());
        static::assertEquals(-1, $definition->getLimit(), "Default limit should be -1");
        static::assertEquals(false, $definition->isDoubleQuote(), "Double quote should be off by default");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvFormat([
            'delimiter'      => "\t",
            'quoteCharacter' => '',
            'nullOutput'     => '',
            'headerRow'      => 1,
            'dataStart'      => 5,
            'lineTerminator' => "----",
            'escape'         => '',
            'limit'          => 2,
            'doubleQuote'    => true,
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->hasQuotes(), "Quoting should be off");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertTrue($definition->hasHeaderRow(), "Headers should be on");
        static::assertEquals(1, $definition->getHeaderRow(), "Header row should be set to 1");
        static::assertEquals(5, $definition->getDataStart(), "Data Start should be set to 5");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
        static::assertEquals('', $definition->getEscapeCharacter(), "Escape Character should be '' (blank)");
        static::assertFalse($definition->hasEscapeCharacter(), "Format should not be marked as not having escape");
        static::assertEquals(2, $definition->getLimit(), 'Limit should be 2');
        static::assertEquals(true, $definition->isDoubleQuote(), 'double quote should be on');
    }

    public function testSettingProperties()
    {
        $definition = new CsvFormat();

        static::assertEquals(',', $definition->getDelimiter(), "Default Delimiter should be ','");
        static::assertEquals('"', $definition->getQuoteCharacter(), "Default quote character should be \"");
        static::assertTrue($definition->hasQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullOutput(), "Null character should be '\\N'");
        static::assertFalse($definition->hasHeaderRow(), "Headers should be off by default");
        static::assertEquals(1, $definition->getDataStart(), "Data start should be 1 by default");
        static::assertEquals("\n", $definition->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertEquals('\\', $definition->getEscapeCharacter(), "Default escape character should be '\\'");
        static::assertTrue($definition->hasEscapeCharacter());
        static::assertEquals(-1, $definition->getLimit(), "Default limit should be -1");
        static::assertEquals(false, $definition->isDoubleQuote(), "Double quote should be off by default");

        static::assertSame($definition, $definition->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($definition, $definition->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $definition->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($definition->hasQuotes(), "Quoting should be off");
        static::assertSame($definition, $definition->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $definition->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($definition, $definition->setHeaderRow(1), "setHeaders should be fluent");
        static::assertTrue($definition->hasHeaderRow(), "Headers should be on");
        static::assertEquals(1, $definition->getHeaderRow(), "Headers should be set to 1");
        static::assertSame($definition, $definition->setDataStart(2), "setDataStart should be fluent");
        static::assertEquals(2, $definition->getDataStart(), "Data Start should be 2");
        static::assertSame($definition, $definition->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $definition->getLineTerminator(), "Line terminator should be '----'");
        static::assertSame($definition, $definition->setEscapeCharacter('"'), "Set escape character should be fluent");
        static::assertEquals('"', $definition->getEscapeCharacter(), "Escape character should be modified");
        static::assertTrue($definition->hasEscapeCharacter(), "Format should have an escape character");
        static::assertSame($definition, $definition->setEscapeCharacter(''), "Set escape character should be fluent");
        static::assertEquals('', $definition->getEscapeCharacter(), "Escape character should be modified");
        static::assertFalse($definition->hasEscapeCharacter(), "Format should not have an escape character");
        static::assertSame($definition, $definition->setLimit(3), "setLimit should be fluent");
        static::assertEquals(3, $definition->getLimit(), "Limit should be modified");
        static::assertSame($definition, $definition->setDoubleQuote(true), 'setDoubleQuote should be fluent');
        static::assertTrue($definition->isDoubleQuote(), 'isDoubleQuote should be true');
    }

    public function testSettingHeaderRowToLargerThanDataStartWillModifyDataStart()
    {
        $definition = new CsvFormat();
        static::assertEquals(-1, $definition->getHeaderRow());
        static::assertEquals(1, $definition->getDataStart());

        $definition->setHeaderRow(2);
        static::assertEquals(2, $definition->getHeaderRow());
        static::assertEquals(3, $definition->getDataStart());

        $definition->setDataStart(5);
        static::assertEquals(2, $definition->getHeaderRow());
        static::assertEquals(5, $definition->getDataStart());

        $definition->setDataStart(1);
        static::assertEquals(2, $definition->getHeaderRow());
        static::assertEquals(3, $definition->getDataStart());

        $definition->setHeaderRow(-1);
        $definition->setDataStart(-1);
        static::assertEquals(-1, $definition->getHeaderRow());
        static::assertEquals(1, $definition->getDataStart());
    }
}
