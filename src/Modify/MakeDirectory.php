<?php

namespace Graze\DataFile\Modify;

use Graze\DataFile\Modify\Exception\MakeDirectoryFailedException;
use Graze\DataFile\Node\LocalFile;

class MakeDirectory
{
    /**
     * Create the directory specified by the $file if it does not exist
     *
     * @param LocalFile $file
     * @param int       $mode
     *
     * @return LocalFile The original file inputted
     * @throws MakeDirectoryFailedException
     */
    public function makeDirectory(LocalFile $file, $mode = 0777)
    {
        if (!($file instanceof LocalFile)) {
            throw new \InvalidArgumentException("Node: $file is not a LocalFile");
        }

        if (!file_exists($file->getDirectory())) {
            if (!@mkdir($file->getDirectory(), $mode, true)) {
                $lastError = error_get_last();
                throw new MakeDirectoryFailedException($file, $lastError['message']);
            }
        }

        return $file;
    }
}
