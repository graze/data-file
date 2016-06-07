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

use Graze\CsvToken\Csv\Bom;
use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;

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
        static::assertEquals('"', $definition->getQuote(), "Default quote character should be \"");
        static::assertTrue($definition->hasQuote(), "Quoting should be on by default");
        static::assertEquals('\\N', $definition->getNullValue(), "Null character should be '\\N'");
        static::assertFalse($definition->hasHeaderRow(), "Headers should be on by default");
        static::assertEquals(-1, $definition->getHeaderRow(), "Header row should be -1 by default");
        static::assertEquals(1, $definition->getDataStart(), "Get data start should be 1 by default");
        static::assertEquals(
            ["\n", "\r", "\r\n"],
            $definition->getNewLines(),
            "Line terminator should be ['\\n','\\r','\\r\\n']"
        );
        static::assertEquals("\n", $definition->getNewLine(), "Line character should be '\\n'");
        static::assertEquals('\\', $definition->getEscape(), "Default escape character should be '\\'");
        static::assertTrue($definition->hasEscape());
        static::assertEquals(-1, $definition->getLimit(), "Default limit should be -1");
        static::assertEquals(false, $definition->useDoubleQuotes(), "Double quote should be off by default");
        static::assertNull($definition->getBom(), "Bom should be null by default");
        static::assertEquals(
            [Bom::BOM_UTF8, Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE, Bom::BOM_UTF32_BE, Bom::BOM_UTF32_LE],
            $definition->getBoms()
        );
        static::assertEquals('UTF-8', $definition->getEncoding(), 'Encoding should be set to UTF-8 by default');
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $definition = new CsvFormat([
            'delimiter'   => "\t",
            'quote'       => '',
            'null'        => '',
            'headerRow'   => 1,
            'dataStart'   => 5,
            'newLine'     => "----",
            'escape'      => '',
            'limit'       => 2,
            'doubleQuote' => true,
            'bom'         => Bom::BOM_UTF16_BE,
            'encoding'    => 'UTF-16BE',
        ]);

        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertEquals('', $definition->getQuote(), "Quote character should be blank");
        static::assertFalse($definition->hasQuote(), "Quoting should be off");
        static::assertEquals('', $definition->getNullValue(), "Null character should be '' (blank)'");
        static::assertTrue($definition->hasHeaderRow(), "Headers should be on");
        static::assertEquals(1, $definition->getHeaderRow(), "Header row should be set to 1");
        static::assertEquals(5, $definition->getDataStart(), "Data Start should be set to 5");
        static::assertEquals("----", $definition->getNewLine(), "Line terminator should be '----'");
        static::assertEquals(["----"], $definition->getNewLines(), "Line terminators should be ['----']");
        static::assertEquals('', $definition->getEscape(), "Escape Character should be '' (blank)");
        static::assertFalse($definition->hasEscape(), "Format should not be marked as not having escape");
        static::assertEquals(2, $definition->getLimit(), 'Limit should be 2');
        static::assertEquals(true, $definition->useDoubleQuotes(), 'double quote should be on');
        static::assertEquals(Bom::BOM_UTF16_BE, $definition->getBom(), 'bom should be set to UTF-16BE');
        static::assertEquals([Bom::BOM_UTF16_BE], $definition->getBoms(), 'boms should be set to [UTF-16BE]');
        static::assertEquals('UTF-16BE', $definition->getEncoding(), 'Encoding should be set to UTF-16BE');
    }

    public function testSettingProperties()
    {
        $definition = new CsvFormat();

        static::assertSame($definition, $definition->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $definition->getDelimiter(), "Delimiter should be set to '\\t' (tab)");

        static::assertSame($definition, $definition->setQuote(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $definition->getQuote(), "Quote character should be blank");
        static::assertFalse($definition->hasQuote(), "Quoting should be off");

        static::assertSame($definition, $definition->setNullValue(''), "setNullOutput should be fluent");
        static::assertEquals('', $definition->getNullValue(), "Null character should be '' (blank)'");

        static::assertSame($definition, $definition->setHeaderRow(1), "setHeaders should be fluent");
        static::assertTrue($definition->hasHeaderRow(), "Headers should be on");
        static::assertEquals(1, $definition->getHeaderRow(), "Headers should be set to 1");

        static::assertSame($definition, $definition->setDataStart(2), "setDataStart should be fluent");
        static::assertEquals(2, $definition->getDataStart(), "Data Start should be 2");

        static::assertSame($definition, $definition->setNewLine('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $definition->getNewLine(), "Line terminator should be '----'");
        static::assertEquals(["----"], $definition->getNewLines(), "Line terminator should be '----'");

        static::assertSame($definition, $definition->setNewLine(['----', '+++']), "setLineTerminator should be fluent");
        static::assertEquals("----", $definition->getNewLine(), "Line terminator should be '----'");
        static::assertEquals(["----", "+++"], $definition->getNewLines(), "Line terminator should be ['----','+++']");

        static::assertSame($definition, $definition->setEscape('"'), "Set escape character should be fluent");
        static::assertEquals('"', $definition->getEscape(), "Escape character should be modified");
        static::assertTrue($definition->hasEscape(), "Format should have an escape character");

        static::assertSame($definition, $definition->setEscape(''), "Set escape character should be fluent");
        static::assertEquals('', $definition->getEscape(), "Escape character should be modified");
        static::assertFalse($definition->hasEscape(), "Format should not have an escape character");

        static::assertSame($definition, $definition->setLimit(3), "setLimit should be fluent");
        static::assertEquals(3, $definition->getLimit(), "Limit should be modified");

        static::assertSame($definition, $definition->setDoubleQuote(true), 'setDoubleQuote should be fluent');
        static::assertTrue($definition->useDoubleQuotes(), 'isDoubleQuote should be true');

        static::assertSame($definition, $definition->setBom(Bom::BOM_UTF32_BE), 'setBom should be fluent');
        static::assertEquals(Bom::BOM_UTF32_BE, $definition->getBom(), 'Bom should be set to the UTF32BE BOM');
        static::assertEquals([Bom::BOM_UTF32_BE], $definition->getBoms(), 'Bom should be set to the UTF32BE BOM');
        static::assertEquals(
            'UTF-32BE',
            $definition->getEncoding(),
            'getEncoding should be modified after setting the BOM'
        );

        static::assertSame($definition, $definition->setBom([Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE]));
        static::assertEquals(Bom::BOM_UTF16_BE, $definition->getBom(), 'Bom should be set to UTF16BE for writing');
        static::assertEquals(
            [Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE],
            $definition->getBoms(),
            'Boms should be set to both UTF16 BOMs'
        );
        static::assertEquals(
            'UTF-16BE',
            $definition->getEncoding(),
            'getEncoding should be modified after setting the BOM as an array'
        );

        // reset
        $definition->setBom(null);
        static::assertEquals(null, $definition->getBom(), 'Bom should be reset to null');
        static::assertEquals(
            [Bom::BOM_UTF8, Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE, Bom::BOM_UTF32_BE, Bom::BOM_UTF32_LE],
            $definition->getBoms(),
            'Bom should be reset to null'
        );
        static::assertEquals(
            'UTF-8',
            $definition->getEncoding(),
            'The encoding should be reset when no BOM is present'
        );

        static::assertSame($definition, $definition->setEncoding('UTF-16'), 'setEncoding should be fluent');
        static::assertEquals('UTF-16', $definition->getEncoding(), 'The encoding should be set to UTF-16');
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

    public function testSettingAnInvalidBomWillThrowAnException()
    {
        $definition = new CsvFormat();

        static::expectException(InvalidArgumentException::class);
        $definition->setBom('INVALID');
    }
}
