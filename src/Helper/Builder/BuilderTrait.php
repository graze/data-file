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

namespace Graze\DataFile\Helper\Builder;

trait BuilderTrait
{
    /**
     * @var BuilderInterface
     */
    protected $builder;

    /**
     * @param BuilderInterface $builder
     *
     * @return $this
     */
    public function setBuilder(BuilderInterface $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @return BuilderInterface
     */
    public function getBuilder()
    {
        if (!$this->builder) {
            $this->builder = new Builder();
        }

        return $this->builder;
    }
}
