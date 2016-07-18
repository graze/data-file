<?php

namespace Graze\DataFile\Node\FileSystem;

use League\Flysystem\Filesystem;

class FilesystemWrapper extends Filesystem implements FilesystemWrapperInterface
{
    /**
     * FilesystemWrapper constructor.
     *
     * @param Filesystem $fileSystem
     */
    public function __construct(Filesystem $fileSystem)
    {
        parent::__construct($fileSystem->getAdapter(), $fileSystem->getConfig());
    }
}
