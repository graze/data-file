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

use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Modify\FileProcessTrait;
use Graze\DataFile\Node\LocalFileNodeInterface;
use Psr\Log\LoggerAwareInterface;

class Zip implements
    CompressionTypeInterface,
    CompressorInterface,
    DeCompressorInterface,
    LoggerAwareInterface,
    BuilderAwareInterface
{
    use GetOptionTrait;
    use FileProcessTrait;
    use OptionalLoggerTrait;
    use CompressorTrait;
    use DeCompressorTrait;

    const NAME = 'zip';

    /**
     * Get the extension used by this compressor
     *
     * @return string
     */
    public function getExtension()
    {
        return 'zip';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return static::NAME;
    }

    /**
     * Get the command line to compress a file
     *
     * @param LocalFileNodeInterface $from
     * @param LocalFileNodeInterface $to
     *
     * @return string
     */
    public function getCompressCommand(LocalFileNodeInterface $from, LocalFileNodeInterface $to)
    {
        return sprintf("zip %s %s", escapeshellarg($to->getPath()), escapeshellarg($from->getPath()));
    }

    /**
     * Get the command line to decompress a file
     *
     * @param LocalFileNodeInterface $from
     * @param LocalFileNodeInterface $to
     *
     * @return string
     *
     */
    public function getDecompressCommand(LocalFileNodeInterface $from, LocalFileNodeInterface $to)
    {
        return sprintf("unzip -p %s > %s", escapeshellarg($from->getPath()), escapeshellarg($to->getPath()));
    }
}
