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

namespace Graze\DataFile\Node;

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatAwareTrait;
use Graze\DataFile\Modify\Exception\CopyFailedException;
use League\Flysystem\File;

class FileNode extends File implements FileNodeInterface, FormatAwareInterface
{
    use FormatAwareTrait;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getPath();
    }

    /**
     * @return mixed
     */
    public function getDirectory()
    {
        return pathinfo($this->path, PATHINFO_DIRNAME) . '/';
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return pathinfo($this->path, PATHINFO_BASENAME);
    }

    /**
     * Returns the contents of the file as an array.
     *
     * @return array
     */
    public function getContents()
    {
        if ($this->exists()) {
            return explode("\n", trim($this->read()));
        } else {
            return [];
        }
    }

    /**
     * @param string|null $newPath
     *
     * @return static
     * @throws CopyFailedException When it is unable to copy the file
     */
    public function copy($newPath = null)
    {
        if (is_null($newPath)) {
            $newPath = $this->path . '-copy';
        }

        if (@$this->filesystem->copy($this->path, $newPath)) {
            return $this->getClone()->setPath($newPath);
        } else {
            $lastError = error_get_last();
            throw new CopyFailedException($this, $newPath, $lastError['message']);
        }
    }

    /**
     * Return a clone of this object
     *
     * @return static
     */
    public function getClone()
    {
        return clone $this;
    }

    /**
     * Clone sub objects
     */
    public function __clone()
    {
        if ($this->format) {
            $this->format = clone $this->format;
        }
    }
}
