<?php

namespace Graze\DataFile\Test\Unit\Modify\Compress;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Test\Modify\Compress\FakeCompressionAware;
use Graze\DataFile\Test\TestCase;

class CompressionAwareTest extends TestCase
{
    public function testDefaultCompressionIs()
    {
        $trait = new FakeCompressionAware();
        static::assertEquals(CompressionFactory::TYPE_NONE, $trait->getCompression());
    }

    public function testSetCompression()
    {
        $trait = new FakeCompressionAware();
        static::assertSame($trait, $trait->setCompression(Gzip::NAME));
        static::assertEquals(Gzip::NAME, $trait->getCompression());
    }
}
