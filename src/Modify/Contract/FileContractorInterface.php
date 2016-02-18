<?php

namespace Graze\DataFile\Modify\Contract;

use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;

interface FileContractorInterface
{
    /**
     * @param FileNodeCollectionInterface $files
     *
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $file
     * @param array                       $options
     *
     * @return FileNodeInterface
     */
    public function contract(
        FileNodeCollectionInterface $files,
        FileNodeInterface $file,
        array $options = []
    );
}
