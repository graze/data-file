<?php

namespace Graze\DataFile\Helper;

use Graze\DataFile\Node\LocalFile;

trait FileHelper
{
    /**
     * @param string|null $basePath
     *
     * @return LocalFile
     */
    public function getTemporaryFile($basePath = null)
    {
        $path = $basePath ?: '/tmp/file/';
        $file = new LocalFile(sprintf(
            '%s%s.tmp',
            $this->addTrailingSlash($path),
            uniqid('file')
        ));

        return $file;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function addTrailingSlash($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
