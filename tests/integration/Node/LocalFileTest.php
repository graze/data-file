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

namespace Graze\DataFile\Test\Integration\Node;

use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;

class LocalFileTest extends AbstractFileTestCase
{
    public function testInstanceOf()
    {
        $file = new LocalFile('/broken/path/to/nowhere');
        static::assertInstanceOf(FileNodeInterface::class, $file);
    }

    public function testGetFilePathReturnsFullyQualifiedPath()
    {
        $file = new LocalFile(static::$dir . 'file_path.test');
        $file->put('random');

        $expected = static::$dir . 'file_path.test';

        static::assertEquals($expected, $file->getPath());
    }

    public function testGetFilenameJustReturnsTheFilename()
    {
        $file = new LocalFile(static::$dir . 'file_name.test');

        static::assertEquals('file_name.test', $file->getFilename());
    }

    public function testFileExists()
    {
        $file = new LocalFile(static::$dir . 'file_exists.test');

        static::assertFalse($file->exists());

        $file->put('random');

        static::assertTrue($file->exists());
    }

    public function testFileGetContents()
    {
        $file = new LocalFile(static::$dir . 'file_get_contents.test');

        $file->put('content stuff');

        static::assertEquals(['content stuff'], $file->getContents());
    }

    public function testFileGetContentsReturnsEmptyArrayIfFileDoesNotExist()
    {
        $file = new LocalFile(static::$dir . 'file_get_contents_empty.test');
        static::assertEquals([], $file->getContents());
    }

    public function testCompression()
    {
        $file = (new LocalFile(static::$dir . 'file_compression.test'))
            ->setCompression(Gzip::NAME);
        static::assertEquals('gzip', $file->getCompression());
    }

    public function testEncoding()
    {
        $file = (new LocalFile(static::$dir . 'file_encoding.test'))
            ->setEncoding('UTF-8');
        static::assertEquals('UTF-8', $file->getEncoding());
    }

    public function testToString()
    {
        $file = new LocalFile(static::$dir . 'to_string.test');
        static::assertEquals($file->getPath(), $file);
    }

    public function testGetContentsForCompressedFile()
    {
        $file = new LocalFile(static::$dir . 'file_uncompressed.test');
        $file->put('uncompressed contents');

        $gzip = new Gzip();
        $compressed = $gzip->compress($file, ['keepOldFile' => true]);

        static::assertEquals(['uncompressed contents'], $compressed->getContents());
    }

    public function testGetContentsForCompressedFileDeletesTheUncompressedFileAfterwards()
    {
        $file = new LocalFile(static::$dir . 'file_uncompressed_todelete.test');
        $file->put('uncompressed contents');

        $gzip = new Gzip();
        $compressed = $gzip->compress($file, ['keepOldFile' => true]);

        $compressed->getContents();

        static::assertFalse(
            file_exists(static::$dir . 'file_uncompressed_todelete'),
            "The uncompressed file should be deleted"
        );
    }

    public function testSetEncodingModifiesTheEncoding()
    {
        $file = new LocalFile(static::$dir . 'file_set_encoding.test');
        $file->put('uncompressed contents');

        $file->setEncoding('us-ascii');

        static::assertEquals('us-ascii', $file->getEncoding());
    }

    public function testSetEncodingReturnsIsFluent()
    {
        $file = new LocalFile(static::$dir . 'file_set_encoding2.test');
        $file->put('uncompressed contents');

        $newFile = $file->setEncoding('us-ascii');

        static::assertNotNull($newFile);
        static::assertSame($file, $newFile);
    }

    public function testSetCompressionModifiesTheEncoding()
    {
        $file = new LocalFile(static::$dir . 'file_set_compression.test');
        $file->put('uncompressed contents');

        $file->setCompression(Gzip::NAME);

        static::assertEquals(Gzip::NAME, $file->getCompression());
    }

    public function testSetCompressionReturnsIsFluent()
    {
        $file = new LocalFile(static::$dir . 'file_set_compression2.test');
        $file->put('uncompressed contents');

        $newFile = $file->setCompression(Gzip::NAME);

        static::assertNotNull($newFile);
        static::assertSame($file, $newFile);
    }

    public function testGetDirectoryReturnsJustTheDirectory()
    {
        $file = new LocalFile(static::$dir . 'file_dont_care.test');

        static::assertEquals(static::$dir, $file->getDirectory());
    }

    public function testGetStream()
    {
        $file = new LocalFile(static::$dir . 'file_get_stream.test');

        $stream = $file->getStream();

        static::assertInternalType('resource', $stream);

        fwrite($stream, 'some text');
        fclose($stream);

        static::assertEquals('some text', $file->read());
    }
}
