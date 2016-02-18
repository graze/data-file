<?php

namespace Graze\DataFile\Modify\Expand;

use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;

interface FileExpanderInterface
{
    /**
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canExpand(FileNodeInterface $file);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeInterface $file
     * @param array             $options
     *
     * @return FileNodeCollectionInterface
     */
    public function expand(FileNodeInterface $file, array $options = []);
}
