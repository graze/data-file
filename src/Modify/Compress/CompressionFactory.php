<?php
/**
 * This file is part of graze/data-file
 *
 * Copyright (c) 2016 Nature Delivered Ltd. <https://www.graze.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license https://github.com/graze/data-file/blob/master/LICENSE.md
 * @link    https://github.com/graze/data-file
 */

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Modify\Exception\InvalidCompressionTypeException;

class CompressionFactory
{
    const TYPE_NONE    = 'none';
    const TYPE_UNKNOWN = 'unknown';

    /**
     * @var CompressorInterface[]
     */
    private $compressors;

    /**
     * @var DeCompressorInterface[]
     */
    private $deCompressors;

    public function __construct()
    {
        // build known compression types
        $gzip = new Gzip();
        $zip = new Zip();

        $this->addCompressor($gzip);
        $this->addCompressor($zip);
    }

    /**
     * @param CompressionTypeInterface $type
     *
     * @return static
     */
    public function addCompressor(CompressionTypeInterface $type)
    {
        if ($type instanceof CompressorInterface) {
            $this->compressors[$type->getName()] = $type;
        }
        if ($type instanceof DeCompressorInterface) {
            $this->deCompressors[$type->getName()] = $type;
        }

        return $this;
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
     * @param string $compression
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
