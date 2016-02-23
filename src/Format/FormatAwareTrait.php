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

trait FormatAwareTrait
{
    /**
     * @var FormatInterface|null
     */
    protected $format = null;

    /**
     * @return FormatInterface|null
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     * @param FormatInterface $format
     *
     * @return $this
     */
    public function setFormat(FormatInterface $format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getFormatType()
    {
        if ($this->format) {
            return $this->format->getType();
        }
        return null;
    }
}
