<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\FileNodeInterface;

interface CompressionTypeInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getExtension();
}
