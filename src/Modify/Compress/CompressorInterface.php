<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\FileNodeInterface;

interface CompressorInterface
{
    /**
     * Compress a file and return the new file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $node, array $options = []);
}
