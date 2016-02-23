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

namespace Graze\DataFile\Test\Helper;

use Graze\DataFile\Helper\GetOptionTrait;

class FakeGetOption
{
    use GetOptionTrait;

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
