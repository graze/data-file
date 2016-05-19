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

trait RowProcessor
{
    /**
     * @var callable[]
     */
    protected $processors = [];

    /**
     * Add a processor
     *
     * @param callable $processor
     *
     * @return static
     */
    public function addProcessor(callable $processor)
    {
        $this->processors[] = $processor;

        return $this;
    }

    /**
     * Go through all the processors
     *
     * @param array $row
     *
     * @return array
     */
    public function process(array $row)
    {
        foreach ($this->processors as $processor) {
            $row = call_user_func($processor, $row);
        }

        return $row;
    }

    /**
     * Remove a processor
     *
     * @param callable $processor
     *
     * @return static
     */
    public function removeProcessor(callable $processor)
    {
        $res = array_search($processor, $this->processors, true);
        if ($res !== false) {
            unset($this->processors[$res]);
        }

        return $this;
    }

    /**
     * Check if a processor has already been added
     *
     * @param callable $processor
     *
     * @return bool
     */
    public function hasProcessor(callable $processor)
    {
        return array_search($processor, $this->processors, true) !== false;
    }
}
