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

namespace Graze\DataFile\Test\Unit\Modify\Encoding;

use Graze\DataFile\Test\Modify\Encoding\FakeEncodingAware;
use Graze\DataFile\Test\TestCase;

class EncodingAwareTest extends TestCase
{
    public function testDefaultEncodingIsBlank()
    {
        $encoding = new FakeEncodingAware();
        static::assertEquals('', $encoding->getEncoding());
    }

    public function testSetEncoding()
    {
        $encoding = new FakeEncodingAware();
        static::assertSame($encoding, $encoding->setEncoding('UTF-8'));
        static::assertEquals('UTF-8', $encoding->getEncoding());
    }
}
