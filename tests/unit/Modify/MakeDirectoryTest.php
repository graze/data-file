<?php

namespace Graze\DataFile\Test\Unit\Modify;

use Graze\DataFile\Modify\Exception\MakeDirectoryFailedException;
use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Test\TestCase;
use League\Flysystem\FilesystemInterface;
use Mockery as m;

class MakeDirectoryTest extends TestCase
{
    public function testWhenCreateDirReturnsFalseAnExceptionIsthrown()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $directory = new FileNode($fileSystem, 'random/path');

        $fileSystem->shouldReceive('createDir')
                   ->with($directory->getDirectory(), ['visibility' => 'public'])
                   ->andReturn(false);
        $this->expectException(MakedirectoryFailedException::class);

        $maker = new MakeDirectory();
        $maker->makeDirectory($directory);
    }
}
