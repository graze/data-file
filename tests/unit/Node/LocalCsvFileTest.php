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

        $format = $clone->getFormat();
        static::assertInstanceOf(CsvFormatInterface::class, $format);
        static::assertInstanceOf(CsvFormat::class, $format);

        static::assertNotSame($file, $clone);

        $format->setDelimiter('--');

        static::assertNotEquals($file->getFormat()->getDelimiter(), $format->getDelimiter());
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
}
