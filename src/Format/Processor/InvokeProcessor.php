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

/**
 * Invoke Trait for processors
 */
trait InvokeProcessor
{
    /**
     * @param array $row
     *
     * @return array
     */
    public function __invoke(array $row)
    {
        return $this->process($row);
    }

    /**
     * @param array $row
     *
     * @return array
     */
    abstract function process(array $row);
}
