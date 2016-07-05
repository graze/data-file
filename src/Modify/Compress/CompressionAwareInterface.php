<?php

namespace Graze\DataFile\Modify\Compress;

interface CompressionAwareInterface
{
    /**
     * @return string
     */
    public function getCompression();

    /**
     * @param string $compression
     *
     * @return static
     */
    public function setCompression($compression);
}
