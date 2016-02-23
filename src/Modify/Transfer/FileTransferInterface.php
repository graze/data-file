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
