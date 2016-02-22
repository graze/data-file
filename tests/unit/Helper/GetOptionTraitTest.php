<?php

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
