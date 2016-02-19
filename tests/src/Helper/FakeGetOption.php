<?php

namespace Graze\DataFile\Test\Helper;

use Graze\DataFile\Helper\GetOption;

class FakeGetOption
{
    use GetOption;

    /**
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param string     $name
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function checkGetOption($name, $default = null)
    {
        return $this->getOption($name, $default);
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function checkRequireOption($name)
    {
        return $this->requireOption($name);
    }
}
