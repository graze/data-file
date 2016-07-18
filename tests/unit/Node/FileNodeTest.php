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
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use Mockery as m;

class FileNodeTest extends TestCase
{
    /**
     * @return mixed Really a Filesystem|MockInterface but coding standards get confused
     */
    private function getFilesystem()
    {
        $fileSystem = m::mock(Filesystem::class);
        $fileSystem->shouldReceive('getAdapter')
                   ->andReturn(m::mock(AdapterInterface::class));
        $fileSystem->shouldReceive('getConfig')
                   ->andReturn(null);
        return $fileSystem;
    }

    public function testInstanceOf()
    {
        $fileSystem = $this->getFilesystem();
        $file = new FileNode($fileSystem, 'not/nop');

        static::assertInstanceOf(FileNodeInterface::class, $file);
        static::assertInstanceOf(FormatAwareInterface::class, $file);
        static::assertInstanceOf(CompressionAwareInterface::class, $file);
        static::assertInstanceOf(EncodingAwareInterface::class, $file);
    }

    public function testEmptyFileWillReturnEmptyArrayForGetContents()
    {
        $fileSystem = $this->getFilesystem();
        $file = new FileNode($fileSystem, 'not/exists');

        $fileSystem->shouldReceive('has')
                   ->with('not/exists')
                   ->andReturn(false);

        static::assertEquals([], $file->getContents());
    }

    public function testWhenCopyFailsItRaisesAnException()
    {
        $fileSystem = $this->getFilesystem();
        $localFile = new FileNode($fileSystem, 'some/random');

        $newPath = new FileNode($fileSystem, 'some/target');

        $fileSystem->shouldReceive('copy')
                   ->with($localFile->getPath(), $newPath->getPath())
                   ->andReturn(false);

        $this->expectException(CopyFailedException::class);

        $localFile->copy($newPath->getPath());
    }
}
