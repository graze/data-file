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

namespace Graze\DataFile\Test\Fuctional\Modify;

use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;
use Mockery as m;

class CopyTest extends AbstractFileTestCase
{
    public function testCopyCreatesADuplicateFile()
    {
        $localFile = new LocalFile(static::$dir . 'copy_orig.test');
        $localFile->put('some random text');

        $newFile = $localFile->copy($localFile->getPath() . '.copy');

        static::assertTrue($newFile->exists());
        static::assertEquals($localFile->getPath() . '.copy', $newFile->getPath());
        static::assertEquals($localFile->getContents(), $newFile->getContents());
    }

    public function testCopyCopiesAttributes()
    {
        $localFile = (new LocalFile(static::$dir . 'copy_attributes.text'))
            ->setEncoding('ascii');
        $localFile->put('some ascii text');

        $newFile = $localFile->copy($localFile->getPath() . '.copy');

        static::assertEquals('ascii', $newFile->getEncoding());

        $gzip = new Gzip();
        $gzipped = $gzip->compress($newFile);

        static::assertEquals(Gzip::NAME, $gzipped->getCompression());

        $gzipCopy = $gzipped->copy($gzipped->getPath() . '.copy');

        static::assertEquals($gzipped->getCompression(), $gzipCopy->getCompression());
    }

    public function testCopyAppendsCopyWhenNoPathIsSpecified()
    {
        $localFile = new LocalFile(static::$dir . 'copy_default_append.text');
        $localFile->put('some random text');

        $newFile = $localFile->copy();

        static::assertEquals($localFile->getPath() . '-copy', $newFile);
    }
}
