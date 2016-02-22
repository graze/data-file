<?php

namespace Graze\DataFile\Node;

interface LocalFileNodeInterface extends FileNodeInterface
{
    /**
     * @return string
     */
    public function getCompression();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @param string $compression - @see CompressionFactory
     *
     * @return static
     */
    public function setCompression($compression);

    /**
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding($encoding);
}
