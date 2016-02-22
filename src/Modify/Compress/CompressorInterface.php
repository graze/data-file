<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\LocalFileNodeInterface;

interface CompressorInterface
{
    /**
     * Compress a file and return the new file
     *
     * @param LocalFileNodeInterface $node
     * @param array                  $options
     *
     * @return LocalFileNodeInterface
     */
    public function compress(LocalFileNodeInterface $node, array $options = []);
}
