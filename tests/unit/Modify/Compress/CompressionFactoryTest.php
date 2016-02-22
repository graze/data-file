<?php

namespace Graze\DataFile\Test\Unit\Modify\Compress;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Compress\Zip;
use Graze\DataFile\Modify\Exception\InvalidCompressionTypeException;
use Graze\DataFile\Test\TestCase;

class CompressionFactoryTest extends TestCase
{
    /**
     * @var CompressionFactory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new CompressionFactory();
    }

    public function testGetCompressorForGzipReturnsGzip()
    {
        $object = $this->factory->getCompressor(Gzip::NAME);
        static::assertInstanceOf(Gzip::class, $object);
        $object = $this->factory->getDeCompressor(Gzip::NAME);
        static::assertInstanceOf(Gzip::class, $object);
    }

    public function testGetCompressorForZipReturnsZip()
    {
        $object = $this->factory->getCompressor(Zip::NAME);
        static::assertInstanceOf(Zip::class, $object);
        $object = $this->factory->getDeCompressor(Zip::NAME);
        static::assertInstanceOf(Zip::class, $object);
    }

    public function testGetCompressorForNoneWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor(CompressionFactory::TYPE_NONE);
    }

    public function testGetDeCompressorForNoneWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor(CompressionFactory::TYPE_NONE);
    }

    public function testGetCompressorForUnknownWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor(CompressionFactory::TYPE_UNKNOWN);
    }

    public function testGetDeCompressorForUnknownWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor(CompressionFactory::TYPE_UNKNOWN);
    }

    public function testGetCompressorForRandomWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor('random');
    }

    public function testGetDeCompressorForRandomWillThrowAnException()
    {
        $this->expectException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor('random');
    }
}
