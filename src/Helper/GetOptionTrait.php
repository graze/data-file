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

namespace Graze\DataFile\Helper;

use InvalidArgumentException;

trait GetOptionTrait
{
    /**
     * @var array
     */
    protected $options = [];

    /**
     * Get an option value
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    protected function getOption($name, $default)
    {
        return (isset($this->options[$name])) ? $this->options[$name] : $default;
    }

    /**
     * @param string $name
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function requireOption($name)
    {
        if (!isset($this->options[$name])) {
            throw new InvalidArgumentException("The option: '$name' is required");
        }
        return $this->options[$name];
    }
}
