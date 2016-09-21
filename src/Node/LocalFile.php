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

use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\Builder\BuilderTrait;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class LocalFile extends FileNode implements LocalFileNodeInterface, NodeStreamInterface, BuilderAwareInterface
{
    use BuilderTrait;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(new FileSystem(new Local('/')), $path);
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->exists() &&
            !in_array($this->getCompression(), [CompressionFactory::TYPE_NONE, CompressionFactory::TYPE_UNKNOWN])
        ) {
            /** @var CompressionFactory $factory */
            $factory = $this->getBuilder()->build(CompressionFactory::class);
            $compressor = $factory->getDeCompressor($this->getCompression());
            $uncompressed = $compressor->decompress($this);
            $content = $uncompressed->getContents();
            $uncompressed->delete();
            return $content;
        } else {
            return parent::getContents();
        }
    }

    /**
     * Get a stream for the given node
     *
     * @param string $mode
     *
     * @return resource
     */
    public function getStream($mode = 'c+')
    {
        return fopen($this->getPath(), $mode);
    }
}
