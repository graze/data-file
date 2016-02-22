<?php

namespace Graze\DataFile\Modify\Compress;

class CompressionFactory
{
    /**
     * @param string $compression CompressionType::
     *
     * @return CompressorInterface
     * @throws InvalidCompressionTypeException
     */
    public function getCompressor($compression)
    {
        switch ($compression) {
            case CompressionType::GZIP:
                return new Gzip();
            case CompressionType::ZIP:
                return new Zip();
            case CompressionType::NONE:
            default:
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
        return $this->getCompressor($compression);
    }
}
