<?php

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\LocalFileNodeInterface;
use InvalidArgumentException;
use Psr\Log\LogLevel;

trait CompressorTrait
{
    /**
     * Compress a file and return the new file
     *
     * @param LocalFileNodeInterface $node
     * @param array                  $options
     *
     * @return LocalFileNodeInterface
     */
    public function compress(LocalFileNodeInterface $node, array $options = [])
    {
        $pathInfo = pathinfo($node->getPath());

        if (!$node->exists()) {
            throw new InvalidArgumentException("The file: $node does not exist");
        }

        $outputFile = $node->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.' . $this->getExtension())
                           ->setCompression($this->getName());

        $this->log(LogLevel::INFO, "Compressing file: {file} into {target} using {compression}", [
            'file'        => $node,
            'target'      => $outputFile,
            'compression' => $this->getName(),
        ]);

        $cmd = $this->getCompressCommand($node, $outputFile);

        $keepOld = (isset($options['keepOldFile'])) ? $options['keepOldFile'] : true;

        return $this->processFile($node, $outputFile, $cmd, $keepOld);
    }

    /**
     * Abstract Log function that might should be handed by the OptionalLoggerTrait or similar
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    abstract protected function log($level, $message, array $context = []);

    /**
     * @param LocalFileNodeInterface $node
     * @param LocalFileNodeInterface $outputFile
     * @param string                 $cmd
     * @param bool                   $keepOld
     *
     * @return LocalFileNodeInterface
     */
    abstract protected function processFile(
        LocalFileNodeInterface $node,
        LocalFileNodeInterface $outputFile,
        $cmd,
        $keepOld = true
    );

    /**
     * @return string
     */
    abstract public function getExtension();

    /**
     * @return string
     */
    abstract public function getName();

    /**
     * Get the command line to compress a file
     *
     * @param LocalFileNodeInterface $from
     * @param LocalFileNodeInterface $to
     *
     * @return string
     */
    abstract public function getCompressCommand(LocalFileNodeInterface $from, LocalFileNodeInterface $to);
}
