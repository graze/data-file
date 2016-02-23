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

namespace Graze\DataFile\Format;

interface FormatAwareInterface
{
    /**
     * Get the format for this object
     *
     * @return FormatInterface|null
     */
    public function getFormat();

    /**
     * Set the format for this object
     *
     * @param FormatInterface $format
     *
     * @return $this
     */
    public function setFormat(FormatInterface $format);

    /**
     * Get the type of format for this object, if there is no format specified null is returned
     *
     * @return string|null
     */
    public function getFormatType();
}
