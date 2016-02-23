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

interface LocalFileNodeInterface extends FileNodeInterface
{
    /**
     * @return string
     */
    public function getCompression();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @param string $compression - @see CompressionFactory
     *
     * @return static
     */
    public function setCompression($compression);

    /**
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding($encoding);
}
