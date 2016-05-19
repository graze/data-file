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

namespace Graze\DataFile\Format\Processor;

use InvalidArgumentException;

class ObjectToStringProcessor implements ProcessorInterface
{
    use InvokeProcessor;

    /**
     * Process the data within an array to ensure it is in the correct format
     *
     * @param array $row
     *
     * @return array
     */
    public function process(array $row)
    {
        foreach ($row as &$item) {
            if (is_object($item)) {
                if (method_exists($item, '__toString')) {
                    $item = $item->__toString();
                } else {
                    throw new InvalidArgumentException("Supplied object does not implement __toString");
                }
            }
        }

        return $row;
    }
}
