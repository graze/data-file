<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\FileNodeInterface;

interface DeCompressorInterface
{
    /**
     * Decompress a file and return the decompressed file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $node, array $options = []);
}
