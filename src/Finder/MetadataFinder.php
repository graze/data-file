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

namespace Graze\DataFile\Finder;

use Graze\ArrayFilter\ArrayFilterInterface;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataStructure\Collection\CollectionInterface;

/**
 * Find files based on their metadata
 */
class MetadataFinder implements FileFinderInterface
{
    /**
     * @var ArrayFilterInterface
     */
    private $filter;

    /**
     * @param ArrayFilterInterface $filter
     */
    public function __construct(ArrayFilterInterface $filter)
    {
        $this->filter = $filter;
    }

    /**
     * @param FileNodeCollectionInterface $files
     *
     * @return CollectionInterface
     */
    public function findFiles(FileNodeCollectionInterface $files)
    {
        return $files->filter(function (FileNode $file) {
            $metadata = $file->getMetadata();
            if ($metadata) {
                return ($this->filter->matches($file->getMetadata()));
            } else {
                return false;
            }
        });
    }
}
