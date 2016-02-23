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

use Graze\DataNode\NodeCollectionInterface;
use Graze\DataStructure\Collection\CollectionInterface;

/**
 * Interface FileNodeCollectionInterface
 *
 * A Collection of FileNodeInterface
 *
 * @package Graze\DataFile\Node\File
 */
interface FileNodeCollectionInterface extends CollectionInterface, NodeCollectionInterface
{
    /**
     * For a given set of files, return any common prefix (i.e. directory, s3 key)
     *
     * @return string|null
     */
    public function getCommonPrefix();
}
