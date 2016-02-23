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

namespace Graze\DataFile\Modify;

use Graze\DataFile\Modify\Exception\MakeDirectoryFailedException;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\LocalFile;

class MakeDirectory
{
    const VISIBILITY_PUBLIC  = 'public';
    const VISIBILITY_PRIVATE = 'private';

    /**
     * Create the directory specified by the $file if it does not exist
     *
     * @param FileNode $file
     * @param string   $visibility public or private visibility
     *
     * @return LocalFile The original file inputted
     * @throws MakeDirectoryFailedException
     */
    public function makeDirectory(FileNode $file, $visibility = null)
    {
        $madeDirectory = $file->getFilesystem()->createDir($file->getDirectory(), [
            'visibility' => $visibility ?: static::VISIBILITY_PUBLIC,
        ]);
        if (!$madeDirectory) {
            throw new MakeDirectoryFailedException($file, error_get_last()['message']);
        }

        return $file;
    }
}
