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

    public function testIsCompression()
    {
        static::assertTrue($this->factory->isCompression(Gzip::NAME));
        static::assertTrue($this->factory->isCompression(Zip::NAME));
        static::assertFalse($this->factory->isCompression('lzop'));
    }
}
