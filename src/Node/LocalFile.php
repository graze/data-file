<?php

namespace Graze\DataFile\Node;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;

class LocalFile extends FileNode implements LocalFileNodeInterface
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
     * @return string
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
}
