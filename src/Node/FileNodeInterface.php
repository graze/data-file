<?php

namespace Graze\DataFile\Node;

use Graze\DataNode\NodeInterface;

interface FileNodeInterface extends NodeInterface
{
    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * Returns the contents of the file as an array.
     *
     * @return array
     */
    public function getContents();

    /**
     * @return bool
     */
    public function exists();

    /**
     * @return bool
     */
    public function delete();
}
