<?php

namespace Graze\DataFile\Finder;

use Graze\ArrayFilter\ArrayFilterInterface;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollectionInterface;

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
     * @inheritdoc
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
