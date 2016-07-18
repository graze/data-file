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

use InvalidArgumentException;
use Traversable;

trait TraversableTrait
{
    /**
     * @param array|Traversable $row
     *
     * @return array
     */
    protected function getArray($row)
    {
        if (!$row instanceof Traversable && !is_array($row)) {
            throw new InvalidArgumentException("The input is not an array or traversable");
        }
        return ($row instanceof Traversable) ? iterator_to_array($row, true) : $row;
    }
}
