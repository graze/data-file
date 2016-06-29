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

namespace Graze\DataFile\Test\Helper\Builder;

class FakeConstructable
{
    /** @var mixed|null */
    private $first;
    /** @var mixed|null */
    private $second;
    /** @var mixed|null */
    private $third;

    /**
     * FakeConstructable constructor.
     *
     * @param mixed|null $first
     * @param mixed|null $second
     * @param mixed|null $third
     */
    public function __construct($first = null, $second = null, $third = null)
    {
        $this->first = $first;
        $this->second = $second;
        $this->third = $third;
    }

    /**
     * @return mixed|null
     */
    public function getFirst()
    {
        return $this->first;
    }

    /**
     * @return mixed|null
     */
    public function getSecond()
    {
        return $this->second;
    }

    /**
     * @return mixed|null
     */
    public function getThird()
    {
        return $this->third;
    }
}
