<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\LocalFileNodeInterface;

interface DeCompressorInterface
{
    /**
     * Decompress a file and return the decompressed file
     *
     * @param LocalFileNodeInterface $node
     * @param array                  $options
     *
     * @return LocalFileNodeInterface
     */
    public function decompress(LocalFileNodeInterface $node, array $options = []);
}
