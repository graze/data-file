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

namespace Graze\DataFile\Modify\Expand;

use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;

interface FileExpanderInterface
{
    /**
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canExpand(FileNodeInterface $file);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeInterface $file
     * @param array             $options
     *
     * @return FileNodeCollectionInterface
     */
    public function expand(FileNodeInterface $file, array $options = []);
}
