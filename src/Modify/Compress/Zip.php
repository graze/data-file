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
use Symfony\Component\Process\Exception\ProcessFailedException;

class Zip implements CompressorInterface, DeCompressorInterface, LoggerAwareInterface, ProcessFactoryAwareInterface
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

        return $this->zip($node, $options);
    }

    /**
     * @extend Graze\DataFile\Node\File\LocalFile
     *
     * @param LocalFile $file
     * @param array     $options -keepOldFile <bool> (Default: true)
     *
     * @return LocalFile
     */
    public function zip(LocalFile $file, array $options = [])
    {
        $this->options = $options;
        $pathInfo = pathinfo($file->getPath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = $file->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.zip')
                           ->setCompression(CompressionType::ZIP);

        $this->log(LogLevel::INFO, "Compressing file: {file} into {target} using {compression}", [
            'file'        => $file,
            'target'      => $outputFile,
            'compression' => CompressionType::ZIP,
        ]);
        $cmd = "zip {$outputFile->getPath()} {$file->getPath()}";

        return $this->processFile($file, $outputFile, $cmd, $this->getOption('keepOldFile', true));
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
        return $this->unzip($node, $options);
    }

    /**
     * @extend Graze\DataFile\Node\File\LocalFile
     *
     * @param LocalFile $file
     * @param array     $options
     *
     * @return FileNodeInterface
     */
    public function unzip(LocalFile $file, array $options = [])
    {
        $this->options = $options;
        $pathInfo = pathinfo($file->getPath());

        if (!$file->exists()) {
            throw new InvalidArgumentException("The file: $file does not exist");
        }

        $outputFile = $file->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'])
                           ->setCompression(CompressionType::NONE);

        $this->log(LogLevel::INFO, "DeCompressing file: {file} into {target} using {compression}", [
            'file'        => $file,
            'target'      => $outputFile,
            'compression' => CompressionType::ZIP,
        ]);

        $cmd = "unzip -p {$file->getPath()} > {$outputFile->getPath()}";

        return $this->processFile($file, $outputFile, $cmd, $this->getOption('keepOldFile', true));
    }
}
