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

namespace Graze\DataFile\Helper;

use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\Node\LocalFile;

trait FileHelper
{
    /**
     * @return BuilderInterface
     */
    abstract public function getBuilder();

    /**
     * @param string|null $basePath
     *
     * @return LocalFile
     */
    public function getTemporaryFile($basePath = null)
    {
        $path = $basePath ?: '/tmp/file/';
        $file = $this->getBuilder()->build(
            LocalFile::class,
            sprintf(
                '%s%s.tmp',
                $this->addTrailingSlash($path),
                uniqid('file')
            )
        );

        return $file;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public function addTrailingSlash($path)
    {
        return rtrim($path, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }
}
