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

namespace Graze\DataFile\Test\Unit\Format\Processor;

use Graze\DataFile\Test\Format\Processor\FakeRowProcessor;
use Graze\DataFile\Test\TestCase;

class RowProcessorTest extends TestCase
{
    /**
     * @var FakeRowProcessor
     */
    private $processor;

    public function setUp()
    {
        $this->processor = new FakeRowProcessor();
    }

    public function testAddProcessor()
    {
        static::assertEquals($this->processor, $this->processor->addProcessor(function ($row) {
            return $row;
        }));
    }

    public function testHasProcessor()
    {
        $processor = function ($row) {
            return $row;
        };

        static::assertFalse($this->processor->hasProcessor($processor));
        $this->processor->addProcessor($processor);
        static::assertTrue($this->processor->hasProcessor($processor));
    }

    public function testRemoveProcessor()
    {
        $processor = function ($row) {
            return $row;
        };

        static::assertFalse($this->processor->hasProcessor($processor));
        $this->processor->addProcessor($processor);
        static::assertTrue($this->processor->hasProcessor($processor));
        $this->processor->removeProcessor($processor);
        static::assertFalse($this->processor->hasProcessor($processor));
    }

    public function testProcess()
    {
        $calledRow = [];

        $processor = function (array $row) use (&$calledRow) {
            $calledRow = $row;
            return array_map(function ($item) {
                return str_pad($item, 2);
            }, $row);
        };

        $this->processor->addProcessor($processor);

        static::assertEquals(['a ', 'b ', 'c '], $this->processor->process(['a', 'b', 'c']));
        static::assertEquals(['a', 'b', 'c'], $calledRow);
    }
}
