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

use Akamon\MockeryCallableMock\MockeryCallableMock;
use ArrayIterator;
use Graze\DataFile\Helper\MapIterator;
use Graze\DataFile\Test\TestCase;
use Mockery as m;

class MapIteratorTest extends TestCase
{
    public function testMapIterator()
    {
        $callback = new MockeryCallableMock();
        $iterator = new ArrayIterator(['key' => 'value', 'second' => 'monkey', 'third']);

        $mapIterator = new MapIterator($iterator, $callback);

        $callback->shouldBeCalled()
                 ->with('value', 'key', $iterator)
                 ->andReturn('modified');
        $callback->shouldBeCalled()
                 ->with('monkey', 'second', $iterator)
                 ->andReturn('fish');
        $callback->shouldBeCalled()
                 ->with('third', 0, $iterator)
                 ->andReturn('cake');

        $results = iterator_to_array($mapIterator, true);

        static::assertEquals(
            [
                'key'    => 'modified',
                'second' => 'fish',
                'cake',
            ],
            $results
        );
    }
}
