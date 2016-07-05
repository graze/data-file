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

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Modify\Compress\CompressionAwareInterface;
use Graze\DataFile\Modify\Encoding\EncodingAwareInterface;
use Graze\DataFile\Modify\Exception\CopyFailedException;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Test\TestCase;
use League\Flysystem\FilesystemInterface;
use Mockery as m;

class FileNodeTest extends TestCase
{
    public function testInstanceOf()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $file = new FileNode($fileSystem, 'not/nop');

        static::assertInstanceOf(FileNodeInterface::class, $file);
        static::assertInstanceOf(FormatAwareInterface::class, $file);
        static::assertInstanceOf(CompressionAwareInterface::class, $file);
        static::assertInstanceOf(EncodingAwareInterface::class, $file);
    }

    public function testEmptyFileWillReturnEmptyArrayForGetContents()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $file = new FileNode($fileSystem, 'not/exists');

        $fileSystem->shouldReceive('has')
                   ->with('not/exists')
                   ->andReturn(false);

        static::assertEquals([], $file->getContents());
    }

    public function testWhenCopyFailsItRaisesAnException()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $localFile = new FileNode($fileSystem, 'some/random');

        $newPath = new FileNode($fileSystem, 'some/target');

        $fileSystem->shouldReceive('copy')
                   ->with($localFile->getPath(), $newPath->getPath())
                   ->andReturn(false);

        $this->expectException(CopyFailedException::class);

        $localFile->copy($newPath->getPath());
    }
}
