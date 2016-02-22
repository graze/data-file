<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Modify\Exception\InvalidCompressionTypeException;

class CompressionFactory
{
    const TYPE_NONE    = 'none';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @var CompressionTypeInterface[]
     */
    private $compressors;

    /**
     * @var CompressionTypeInterface[]
     */
    private $deCompressors;

    public function __construct()
    {
        // build known compression types
        $gzip = new Gzip();
        $zip = new Zip();

        $this->addCompressor($gzip);
        $this->addDecompressor($gzip);
        $this->addCompressor($zip);
        $this->addDecompressor($zip);
    }

    /**
     * @param CompressionTypeInterface $type
     *
     * @return static
     */
    public function addCompressor(CompressionTypeInterface $type)
    {
        $this->compressors[$type->getName()] = $type;
    }

    /**
     * @param CompressionTypeInterface $type
     *
     * @return static
     */
    public function addDeCompressor(CompressionTypeInterface $type)
    {
        $this->deCompressors[$type->getName()] = $type;
    }

    /**
     * Check if the specified $compression is valid or not
     *
     * @param string $compression
     *
     * @return bool
     */
    public function isCompression($compression)
    {
        return isset($this->compressors[$compression]);
    }

    /**
     * @param string $compression
     *
     * @return CompressorInterface
     * @throws InvalidCompressionTypeException
     */
    public function getCompressor($compression)
    {
        if (isset($this->compressors[$compression])) {
            return $this->compressors[$compression];
        } else {
            throw new InvalidCompressionTypeException($compression);
        }
    }

    /**
     * @param string $compression CompressionType::
     *
     * @return DeCompressorInterface
     * @throws InvalidCompressionTypeException
     */
    public function getDeCompressor($compression)
    {
        if (isset($this->deCompressors[$compression])) {
            return $this->deCompressors[$compression];
        } else {
            throw new InvalidCompressionTypeException($compression);
        }
    }
}
