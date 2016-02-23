<?php

namespace Graze\DataFile\Modify\Compress;

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
