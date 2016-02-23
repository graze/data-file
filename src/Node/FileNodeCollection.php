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

use Graze\DataNode\NodeCollection;
use InvalidArgumentException;

/**
 * Class FileNodeCollection
 *
 * @package Graze\DataFile\Node\File
 */
class FileNodeCollection extends NodeCollection implements FileNodeCollectionInterface
{
    /**
     * For a given set of files, return any common prefix (i.e. directory, s3 key)
     *
     * @return string|null
     */
    public function getCommonPrefix()
    {
        if ($this->count() == 0) {
            return null;
        }

        $commonPath = $this->reduce(function ($commonPath, FileNodeInterface $file) {
            if (is_null($commonPath)) {
                return $file->getPath();
            }
            return $this->getCommonPrefixString($commonPath, $file->getPath());
        });

        return (strlen($commonPath) > 0) ? $commonPath : null;
    }

    /**
     * @param string $left
     * @param string $right
     *
     * @return string
     */
    private function getCommonPrefixString($left, $right)
    {
        for ($i = 1; $i < strlen($left); $i++) {
            if (substr_compare($left, $right, 0, $i) !== 0) {
                return substr($left, 0, $i - 1);
            }
        }
        return substr($left, 0, $i);
    }

    /**
     * {@inheritdoc}
     */
    public function add($value)
    {
        if (!($value instanceof FileNodeInterface)) {
            throw new InvalidArgumentException("The specified value does not implement FileNodeInterface");
        }
        return parent::add($value);
    }
}
