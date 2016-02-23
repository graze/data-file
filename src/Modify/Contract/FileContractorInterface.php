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

namespace Graze\DataFile\Modify\Contract;

use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;

interface FileContractorInterface
{
    /**
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     *
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files, FileNodeInterface $target);

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     * @param array                       $options
     *
     * @return FileNodeInterface
     */
    public function contract(
        FileNodeCollectionInterface $files,
        FileNodeInterface $target,
        array $options = []
    );
}
