<?php

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
