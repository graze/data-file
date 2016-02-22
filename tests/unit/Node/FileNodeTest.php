<?php

namespace Graze\DataFile\Test\Integration\Node;

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
}
