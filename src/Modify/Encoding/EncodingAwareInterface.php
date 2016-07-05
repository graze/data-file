<?php

namespace Graze\DataFile\Modify\Encoding;

interface EncodingAwareInterface
{
    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding($encoding);
}
