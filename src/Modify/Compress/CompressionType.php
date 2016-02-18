<?php

namespace Graze\DataFile\Modify\Compress;

class CompressionType
{
    const GZIP    = 'gzip';
    const ZIP     = 'zip';
    const NONE    = 'none';
    const UNKNOWN = 'unknown';

    /**
     * @return array
     */
    public static function getCompressionTypes()
    {
        return [
            static::GZIP,
            static::ZIP
        ];
    }
}
