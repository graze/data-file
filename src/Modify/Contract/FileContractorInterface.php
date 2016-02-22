<?php

namespace Graze\DataFile\Modify\Contract;

use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;

interface FileContractorInterface
{
    /**
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     *
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files, FileNodeInterface $target);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     * @param array                       $options
     *
     * @return FileNodeInterface
     */
    public function contract(
        FileNodeCollectionInterface $files,
        FileNodeInterface $target,
        array $options = []
    );
}
