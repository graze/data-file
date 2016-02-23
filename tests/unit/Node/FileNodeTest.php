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

use Graze\DataFile\Modify\Exception\CopyFailedException;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Test\TestCase;
use League\Flysystem\FilesystemInterface;
use Mockery as m;

class FileNodeTest extends TestCase
{
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
