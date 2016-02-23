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
