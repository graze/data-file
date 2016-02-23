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
