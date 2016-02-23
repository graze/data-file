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

namespace Graze\DataFile\Test\Unit\Format;

use Graze\DataFile\Test\Format\FakeFormatAware;
use Graze\DataFile\Test\TestCase;
use Mockery as m;

class FormatAwareTraitTest extends TestCase
{
    /**
     * @var FakeFormatAware
     */
    protected $formatAware;

    public function setUp()
    {
        $this->formatAware = new FakeFormatAware();
    }

    public function testSetFormat()
    {
        $format = m::mock('Graze\DataFile\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        static::assertSame($format, $this->formatAware->getFormat());
    }

    public function testGetFormatType()
    {
        $format = m::mock('Graze\DataFile\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        $format->shouldReceive('getType')
               ->andReturn('test_format');

        static::assertEquals('test_format', $this->formatAware->getFormatType());
    }

    public function testGetFormatTypeWillReturnNullWithNoFormatIsSpecified()
    {
        static::assertNull($this->formatAware->getFormatType());
    }

    public function testCloningWillCloneFormat()
    {
        $format = m::mock('Graze\DataFile\Format\FormatInterface');
        $this->formatAware->setFormat($format);

        $newFormatAware = clone $this->formatAware;

        static::assertNotSame($this->formatAware, $newFormatAware);
        static::assertNotSame($this->formatAware->getFormat(), $newFormatAware->getFormat());
    }
}
