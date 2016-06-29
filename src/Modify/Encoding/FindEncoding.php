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

namespace Graze\DataFile\Modify\Encoding;

use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Modify\FileModifierInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFileNodeInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FindEncoding implements BuilderAwareInterface, LoggerAwareInterface, FileModifierInterface
{
    use ProcessTrait;
    use OptionalLoggerTrait;

    /**
     * Find the Encoding of a specified file
     *
     * @param LocalFileNodeInterface $file
     *
     * @return null|string
     * @throws ProcessFailedException
     */
    public function getEncoding(LocalFileNodeInterface $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->getProcess($cmd);
        $process->mustRun();

        $result = $process->getOutput();
        if (preg_match('/charset=([^\s]+)/i', $result, $matches)) {
            $this->log(LogLevel::DEBUG, "Found the encoding for '{file}' as '{encoding}'", [
                'file'     => $file,
                'encoding' => $matches[1],
            ]);
            return $matches[1];
        }
        return null;
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
     * @throws InvalidArgumentException
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        if (!$this->canModify($file) || !($file instanceof LocalFileNodeInterface)) {
            throw new InvalidArgumentException(
                "The specified file: '$file' does not implement LocalFileNodeInterface'"
            );
        }

        $file->setEncoding($this->getEncoding($file));

        return $file;
    }
}
