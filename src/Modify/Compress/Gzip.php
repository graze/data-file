<?php

namespace Graze\DataFile\Modify\Compress;

class Gzip extends AbstractCompressor
{
    /**
     * Get the extension used by this compressor
     *
     * @return string
     */
    protected function getExtension()
    {
        return 'gz';
    }

    /**
     * @return string
     */
    protected function getCompression()
    {
        return CompressionType::GZIP;
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
        return sprintf("gzip -c %s > %s", escapeshellarg($fromPath), escapeshellarg($toPath));
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
        return sprintf("gunzip -c %s > %s", escapeshellarg($fromPath), escapeshellarg($toPath));
    }
}
