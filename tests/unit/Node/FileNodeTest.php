<?php

namespace Graze\DataFile\Test\Integration\Node;

use Graze\DataFile\Modify\Exception\CopyFailedException;
use Graze\DataFile\Modify\MakeDirectory;
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
