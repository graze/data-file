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

namespace Graze\DataFile\Test\Unit\Node;

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\TestCase;

class LocalCsvFileTest extends TestCase
{
    public function testCloneWillCloneTheCsvDefinition()
    {
        $file = (new LocalFile('some/path/here'))
            ->setFormat(new CsvFormat());
        $clone = $file->getClone();

        static::assertNotSame($file, $clone);

        $clone->getFormat()->setDelimiter('--');

        static::assertNotEquals($file->getFormat()->getDelimiter(), $clone->getFormat()->getDelimiter());
    }

    public function testImplementsInterface()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        static::assertInstanceOf(FormatAwareInterface::class, $file);
        static::assertInstanceOf(FormatInterface::class, $file->getFormat());
        static::assertInstanceOf(CsvFormatInterface::class, $file->getFormat());
    }

    public function testFormatTypeIsCsv()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        static::assertEquals('csv', $file->getFormatType());
    }

    public function testDefaultsAreAssignedWhenNoOptionsSupplied()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());

        $format = $file->getFormat();

        static::assertInstanceOf(CsvFormatInterface::class, $format);

        static::assertEquals(',', $format->getDelimiter(), "Default Delimiter should be ','");
        static::assertTrue($format->hasQuotes(), "Quoting should be on by default");
        static::assertEquals('\\N', $format->getNullOutput(), "Null character should be '\\N'");
        static::assertTrue($format->hasHeaders(), "Headers should be on by default");
        static::assertEquals(1, $format->getHeaders(), "Headers should have a default of 1");
        static::assertEquals("\n", $format->getLineTerminator(), "Line terminator should be '\\n'");
        static::assertEquals('"', $format->getQuoteCharacter(), "Default quote character should be \"");
    }

    public function testAssigningOptionsModifiesTheDefinition()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat([
                'delimiter'      => "\t",
                'quoteCharacter' => '',
                'nullOutput'     => '',
                'headers'        => 0,
                'lineTerminator' => "----",
            ]));

        $format = $file->getFormat();

        static::assertEquals("\t", $format->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertFalse($format->hasQuotes(), "Quoting should be off");
        static::assertEquals('', $format->getNullOutput(), "Null character should be '' (blank)'");
        static::assertFalse($format->hasHeaders(), "Headers should be off");
        static::assertEquals("----", $format->getLineTerminator(), "Line terminator should be '----'");
        static::assertEquals(
            '',
            $format->getQuoteCharacter(),
            "Default quote character should be blank when useQuotes is false"
        );
    }

    public function testSettingOptionsModifiesTheDefinition()
    {
        $file = (new LocalFile('fake/path'))
            ->setFormat(new CsvFormat());
        $format = $file->getFormat();

        static::assertSame($format, $format->setDelimiter("\t"), "SetDelimiter should be fluent");
        static::assertEquals("\t", $format->getDelimiter(), "Delimiter should be set to '\\t' (tab)");
        static::assertSame($format, $format->setQuoteCharacter(''), "setQuoteCharacter should be fluent");
        static::assertEquals('', $format->getQuoteCharacter(), "Quote character should be blank");
        static::assertFalse($format->hasQuotes(), "Quoting should be off");
        static::assertSame($format, $format->setNullOutput(''), "setNullOutput should be fluent");
        static::assertEquals('', $format->getNullOutput(), "Null character should be '' (blank)'");
        static::assertSame($format, $format->setHeaders(0), "setHeaders should be fluent");
        static::assertFalse($format->hasHeaders(), "Headers should be off");
        static::assertEquals(0, $format->getHeaders(), "Headers should be 0");
        static::assertSame($format, $format->setLineTerminator('----'), "setLineTerminator should be fluent");
        static::assertEquals("----", $format->getLineTerminator(), "Line terminator should be '----'");
    }
}
