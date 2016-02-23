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

namespace Graze\DataFile\Modify\Compress;

use Graze\DataFile\Node\LocalFileNodeInterface;
use InvalidArgumentException;
use Psr\Log\LogLevel;

trait DeCompressorTrait
{
    /**
     * Decompress a file and return the decompressed file
     *
     * @param LocalFileNodeInterface $node
     * @param array                  $options
     *
     * @return LocalFileNodeInterface
     */
    public function decompress(LocalFileNodeInterface $node, array $options = [])
    {
        $pathInfo = pathinfo($node->getPath());

        if (!$node->exists()) {
            throw new InvalidArgumentException("The file: $node does not exist");
        }

        $outputFile = $node->getClone()
                           ->setPath($pathInfo['dirname'] . '/' . $pathInfo['filename'])
                           ->setCompression(CompressionFactory::TYPE_NONE);

        $this->log(LogLevel::INFO, "DeCompressing file: {file} into {target} using {compression}", [
            'file'        => $node,
            'target'      => $outputFile,
            'compression' => $this->getName(),
        ]);

        $cmd = $this->getDecompressCommand($node, $outputFile);

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
     * Get the command line to decompress a file
     *
     * @param LocalFileNodeInterface $from
     * @param LocalFileNodeInterface $to
     *
     * @return string
     */
    abstract public function getDecompressCommand(LocalFileNodeInterface $from, LocalFileNodeInterface $to);
}
