<?php

namespace Graze\DataFile\Helper;

use InvalidArgumentException;

trait GetOption
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
