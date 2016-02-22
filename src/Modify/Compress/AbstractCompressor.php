<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Modify\FileProcessTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

abstract class AbstractCompressor implements
    CompressorInterface,
    DeCompressorInterface,
    LoggerAwareInterface,
    ProcessFactoryAwareInterface
{
    use OptionalLoggerTrait;
    use FileProcessTrait;
    use GetOptionTrait;

    /**
     * Compress a file and return the new file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function compress(FileNodeInterface $node, array $options = [])
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be a LocalFile");
        }

        $this->options = $options;
        $pathInfo = pathinfo($node->getPath());

        if (!$node->exists()) {
            throw new InvalidArgumentException("The file: $node does not exist");
        }

        $outputFile = $node->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $this->getExtension())
                           ->setCompression($this->getCompression());

        $this->log(LogLevel::INFO, "Compressing file: {file} into {target} using {compression}", [
            'file'        => $node,
            'target'      => $outputFile,
            'compression' => $this->getCompression(),
        ]);

        $cmd = $this->getCompressCommand($node->getPath(), $outputFile->getPath());

        return $this->processFile($node, $outputFile, $cmd, $this->getOption('keepOldFile', true));
    }

    /**
     * Decompress a file and return the decompressed file
     *
     * @param FileNodeInterface $node
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function decompress(FileNodeInterface $node, array $options = [])
    {
        if (!($node instanceof LocalFile)) {
            throw new InvalidArgumentException("Node: $node should be a LocalFile");
        }

        $this->options = $options;
        $pathInfo = pathinfo($node->getPath());

        if (!$node->exists()) {
            throw new InvalidArgumentException("The file: $node does not exist");
        }

        $outputFile = $node->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'])
                           ->setCompression(CompressionType::NONE);

        $this->log(LogLevel::INFO, "DeCompressing file: {file} into {target} using {compression}", [
            'file'        => $node,
            'target'      => $outputFile,
            'compression' => $this->getCompression(),
        ]);

        $cmd = $this->getDecompressCommand($node->getPath(), $outputFile->getPath());

        return $this->processFile($node, $outputFile, $cmd, $this->getOption('keepOldFile', true));
    }

    /**
     * Get the extension used by this compressor
     *
     * @return string
     */
    abstract protected function getExtension();

    /**
     * @return string
     */
    abstract protected function getCompression();

    /**
     * Get the command line to compress a file
     *
     * @param string $fromPath
     * @param string $toPath
     *
     * @return string
     */
    abstract protected function getCompressCommand($fromPath, $toPath);

    /**
     * Get the command line to decompress a file
     *
     * @param string $fromPath
     * @param string $toPath
     *
     * @return string
     */
    abstract protected function getDecompressCommand($fromPath, $toPath);
}
