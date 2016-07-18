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

interface CompressionAwareInterface
{
    /**
     * @return string
     */
    public function getCompression();

    /**
     * @param string $compression
     *
     * @return static
     */
    public function setCompression($compression);
}
