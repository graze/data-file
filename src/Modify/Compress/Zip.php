<?php

namespace Graze\DataFile\Modify\Compress;

class Zip extends AbstractCompressor
{
    /**
     * Get the extension used by this compressor
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'zip';
    }

    /**
     * @return string
     */
    protected function getCompression()
    {
        return CompressionType::ZIP;
    }

    /**
     * Get the command line to compress a file
     *
     * @param string $fromPath
     * @param string $toPath
     *
     * @return string
     */
    protected function getCompressCommand($fromPath, $toPath)
    {
        return sprintf("zip %s %s", escapeshellarg($toPath), escapeshellarg($fromPath));
    }

    /**
     * Get the command line to decompress a file
     *
     * @param string $fromPath
     * @param string $toPath
     *
     * @return string
     */
    protected function getDecompressCommand($fromPath, $toPath)
    {
        return sprintf("unzip -p %s > %s", escapeshellarg($fromPath), escapeshellarg($toPath));
    }
}
