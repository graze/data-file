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

namespace Graze\DataFile\Test\Integration\Modify\Compress;

use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\CompressorInterface;
use Graze\DataFile\Modify\Compress\DeCompressorInterface;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GzipTest extends AbstractFileTestCase
{
    /**
     * @var Gzip
     */
    protected $gzip;

    public function setUp()
    {
        $this->gzip = new Gzip();
    }

    public function testInstanceOfCompressorInterface()
    {
        static::assertInstanceOf(CompressorInterface::class, $this->gzip);
        static::assertInstanceOf(DeCompressorInterface::class, $this->gzip);
    }

    public function testFileGetsCompressedAsGzip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_gz.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->compress($file);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_gz.gz', $compressedFile->getPath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(Gzip::NAME, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getPath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testFileGetsDecompressedFromGzip()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_gz.test');
        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->compress($file);
        $uncompressedFile = $this->gzip->decompress($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_gz', $uncompressedFile->getPath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionFactory::TYPE_NONE, $uncompressedFile->getCompression());

        $cmd = "file {$uncompressedFile->getPath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(0, $result, "File should not be compressed");
    }

    public function testCallingGzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gzip.test');

        $this->expectException(InvalidArgumentException::class);

        $this->gzip->compress($file);
    }

    public function testCallingGunzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gunzip.test');

        $this->expectException(InvalidArgumentException::class);

        $this->gzip->decompress($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGzip()
    {
        $process = m::mock(Process::class)->makePartial();
        $builder = m::mock(BuilderInterface::class);
        $builder->shouldReceive('build')
                ->andReturn($process);
        $this->gzip->setBuilder($builder);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_gzip_process.test');

        $file->put('random stuff and things 2!');

        $this->expectException(ProcessFailedException::class);

        $this->gzip->compress($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGunzip()
    {
        $process = m::mock(Process::class)->makePartial();
        $builder = m::mock(BuilderInterface::class);
        $builder->shouldReceive('build')
                ->andReturn($process);
        $this->gzip->setBuilder($builder);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_gunzip_process.test');

        $file->put('random stuff and things 2!');

        $this->expectException(ProcessFailedException::class);

        $this->gzip->decompress($file);
    }

    public function testPassingTheKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'keep_file_gz.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->compress($file, ['keepOldFile' => true]);

        static::assertTrue($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->decompress($compressedFile, ['keepOldFile' => true]);

        static::assertTrue($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingFalseToKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'delete_file_gz.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->compress($file, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->decompress($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }
}
