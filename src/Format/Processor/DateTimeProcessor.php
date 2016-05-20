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

use DateTimeInterface;

class DateTimeProcessor implements ProcessorInterface
{
    use InvokeProcessor;

    const FORMAT = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    private $format;

    /**
     * @param string|null $format Custom DateTime format to use (Defaults to: Y-m-d H:i:s)
     */
    public function __construct($format = null)
    {
        $this->format = $format ?: static::FORMAT;
    }

    /**
     * @param array $row
     *
     * @return array
     */
    public function process(array $row)
    {
        foreach ($row as &$item) {
            if ($item instanceof DateTimeInterface) {
                $item = $item->format($this->format);
            }
        }

        return $row;
    }
}
