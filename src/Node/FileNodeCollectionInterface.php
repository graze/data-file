<?php

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
