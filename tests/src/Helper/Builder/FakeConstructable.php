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
    private $a;
    /** @var mixed|null */
    private $b;
    /** @var mixed|null */
    private $c;

    /**
     * FakeConstructable constructor.
     *
     * @param mixed|null $a
     * @param mixed|null $b
     * @param mixed|null $c
     */
    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a = $a;
        $this->b = $b;
        $this->c = $c;
    }

    /**
     * @return mixed|null
     */
    public function getA()
    {
        return $this->a;
    }

    /**
     * @return mixed|null
     */
    public function getB()
    {
        return $this->b;
    }

    /**
     * @return mixed|null
     */
    public function getC()
    {
        return $this->c;
    }
}
