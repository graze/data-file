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

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Modify\FileModifierInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFileNodeInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class FindCompression implements ProcessFactoryAwareInterface, LoggerAwareInterface, FileModifierInterface
{
    use ProcessTrait;
    use OptionalLoggerTrait;

    /**
     * @var CompressionFactory
     */
    private $factory;

    /**
     * FindCompression constructor.
     *
     * @param CompressionFactory $factory
     */
    public function __construct(CompressionFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * Find the compression of a file
     *
     * @param LocalFileNodeInterface $file
     *
     * @return string
     */
    public function getCompression(LocalFileNodeInterface $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->getProcess($cmd);
        $process->mustRun();

        $result = $process->getOutput();
        if (preg_match('/compressed-encoding=application\/(?:x-)?(.+?);/i', $result, $matches)) {
            if ($this->factory->isCompression($matches[1])) {
                $this->log(LogLevel::DEBUG, "Found the compression for '{file}' as '{compression}'", [
                    'file'        => $file,
                    'compression' => $matches[1],
                ]);
                return $matches[1];
            }
            return CompressionFactory::TYPE_UNKNOWN;
        }
        return CompressionFactory::TYPE_NONE;
    }

    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canModify(FileNodeInterface $file)
    {
        return ($file instanceof LocalFileNodeInterface &&
            $file->exists());
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        if (!$this->canModify($file) || !($file instanceof LocalFileNodeInterface)) {
            throw new InvalidArgumentException("The supplied file: '$file' does not implement LocalFileNodeInterface'");
        }

        $file->setCompression($this->getCompression($file));

        return $file;
    }
}
