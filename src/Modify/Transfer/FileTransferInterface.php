<?php

namespace Graze\DataFile\Modify\Transfer;

use Graze\DataFile\Node\FileNode;

interface FileTransferInterface
{
    /**
     * Copy a file to another file irrespective of filesystem
     *
     * @param FileNode $from
     * @param FileNode $to
     *
     * @return FileNode (Returns $to)
     */
    public function copyTo(FileNode $from, FileNode $to);

    /**
     * Move a file to another file irrespective of filesystem
     *
     * @param FileNode $from
     * @param FileNode $to
     *
     * @return FileNode (Returns $to)
     */
    public function moveTo(FileNode $from, FileNode $to);
}
