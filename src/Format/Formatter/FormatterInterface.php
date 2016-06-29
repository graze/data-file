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

namespace Graze\DataFile\Format\Formatter;

/**
 * Format data from one form into another
 */
interface FormatterInterface
{
    /**
     * Return an initial block if required
     *
     * @return string
     */
    public function getInitialBlock();

    /**
     * Get a separator between each row
     *
     * @return string
     */
    public function getRowSeparator();

    /**
     * Return a closing block if required
     *
     * @return string
     */
    public function getClosingBlock();

    /**
     * @param array|\Traversable $row
     *
     * @return string
     */
    public function format($row);
}
