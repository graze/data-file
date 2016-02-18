<?php

namespace Graze\DataFile\Test\Unit\Modify\Compress;

use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Compress\InvalidCompressionTypeException;
use Graze\DataFile\Modify\Compress\Zip;
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
        $object = $this->factory->getCompressor(CompressionType::GZIP);
        static::assertInstanceOf(Gzip::class, $object);
        $object = $this->factory->getDeCompressor(CompressionType::GZIP);
        static::assertInstanceOf(Gzip::class, $object);
    }

    public function testGetCompressorForZipReturnsZip()
    {
        $object = $this->factory->getCompressor(CompressionType::ZIP);
        static::assertInstanceOf(Zip::class, $object);
        $object = $this->factory->getDeCompressor(CompressionType::ZIP);
        static::assertInstanceOf(Zip::class, $object);
    }

    public function testGetCompressorForNoneWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor(CompressionType::NONE);
    }

    public function testGetDeCompressorForNoneWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor(CompressionType::NONE);
    }

    public function testGetCompressorForUnknownWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor(CompressionType::UNKNOWN);
    }

    public function testGetDeCompressorForUnknownWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor(CompressionType::UNKNOWN);
    }

    public function testGetCompressorForRandomWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getCompressor('random');
    }

    public function testGetDeCompressorForRandomWillThrowAnException()
    {
        static::setExpectedException(InvalidCompressionTypeException::class);
        $this->factory->getDeCompressor('random');
    }
}
