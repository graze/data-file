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

interface ProcessorInterface
{
    /**
     * Process the data within an array to ensure it is in the correct format
     *
     * @param array $row
     *
     * @return array
     */
    public function process(array $row);
}
