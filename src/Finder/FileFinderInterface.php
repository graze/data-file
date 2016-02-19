<?php

namespace Graze\DataFile\Finder;

use Graze\DataFile\Node\FileNodeCollectionInterface;

interface FileFinderInterface
{
    /**
     * @param FileNodeCollectionInterface $files
     *
     * @return FileNodeCollectionInterface
     */
    public function findFiles(FileNodeCollectionInterface $files);
}
