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

namespace Graze\DataFile\Test\Unit\Helper;

use Graze\DataFile\Test\Helper\FakeGetOption;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;

class GetOptionTraitTest extends TestCase
{
    public function testGetOptionWillReturnTheOption()
    {
        $fake = new FakeGetOption();
        $fake->setOptions([
            'name' => 'value',
        ]);
        static::assertEquals('value', $fake->checkGetOption('name'));
    }

    public function testGetOptionWillReturnADefaultValueIfTheValueDoesNotExist()
    {
        $fake = new FakeGetOption();
        static::assertEquals('default', $fake->checkGetOption('name', 'default'));
    }

    public function testRequireOptionWillReturnAnOptionIfItExists()
    {
        $fake = new FakeGetOption();
        $fake->setOptions([
            'name' => 'value',
        ]);
        static::assertEquals('value', $fake->checkRequireOption('name'));
    }

    public function testRequireOptionWillThrowAnExceptionIfTheOptionDoesNotExist()
    {
        $fake = new FakeGetOption();
        $this->expectException(InvalidArgumentException::class);
        $fake->checkRequireOption('name');
    }
}
