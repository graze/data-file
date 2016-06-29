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

use Graze\DataFile\Modify\Compress\CompressionFactory;
use GuzzleHttp\Psr7\LazyOpenStream;
use GuzzleHttp\Psr7\Stream;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

class LocalFile extends FileNode implements LocalFileNodeInterface, NodeStreamInterface
{
    /**
     * @var string
     */
    protected $compression;

    /**
     * @var string|null
     */
    protected $encoding;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        parent::__construct(new FileSystem(new Local('/')), $path);

        $this->compression = CompressionFactory::TYPE_NONE;
        $this->encoding = null;
    }

    /**
     * @return string|null
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContents()
    {
        if ($this->exists() &&
            $this->getCompression() != CompressionFactory::TYPE_NONE
        ) {
            $factory = new CompressionFactory();
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
     * @return string - see CompressionFactory
     */
    public function getCompression()
    {
        return $this->compression;
    }

    /**
     * @param string $compression - @see CompressionFactory
     *
     * @return $this
     */
    public function setCompression($compression)
    {
        $this->compression = $compression;

        return $this;
    }

    /**
     * Get a stream for the given node
     *
     * @param string $mode
     *
     * @return StreamInterface
     */
    public function getStream($mode = 'c+')
    {
        return new LazyOpenStream($this->getPath(), $mode);
    }
}
