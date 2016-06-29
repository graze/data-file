<?php

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
