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

use Graze\DataFile\Helper\Builder\Builder;
use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\CompressorInterface;
use Graze\DataFile\Modify\Compress\DeCompressorInterface;
use Graze\DataFile\Modify\Compress\Zip;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ZipTest extends AbstractFileTestCase
{
    /**
     * @var Zip
     */
    protected $zip;

    /**
     * @var BuilderInterface|MockInterface
     */
    private $builder;

    public function setUp()
    {
        $this->builder = m::mock(Builder::class)->makePartial();
        $this->zip = new Zip();
        $this->zip->setBuilder($this->builder);
    }

    public function testInstanceOfCompressorInterface()
    {
        static::assertInstanceOf(CompressorInterface::class, $this->zip);
        static::assertInstanceOf(DeCompressorInterface::class, $this->zip);
    }

    public function testFileGetsCompressedAsZip()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_zip.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->zip->compress($file);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_zip.zip', $compressedFile->getPath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(Zip::NAME, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getPath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as zip");
    }

    public function testFileGetsDecompressedFromZip()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_zip.test');
        $file->put('random stuff and things!');

        $compressedFile = $this->zip->compress($file);

        static::assertTrue($compressedFile->exists());
        $uncompressedFile = $this->zip->decompress($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_zip', $uncompressedFile->getPath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionFactory::TYPE_NONE, $uncompressedFile->getCompression());

        $cmd = "file {$uncompressedFile->getPath()} | grep " . escapeshellarg('\bzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(0, $result, "File should not be compressed");
    }

    public function testCallingZipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_zip.test');

        $this->expectException(InvalidArgumentException::class);

        $this->zip->compress($file);
    }

    public function testCallingUnzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_zip.zip');

        $this->expectException(InvalidArgumentException::class);

        $this->zip->decompress($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsthrownOnZip()
    {
        $process = m::mock(Process::class)->makePartial();
        $this->builder->shouldReceive('build')
                      ->andReturn($process);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_zip_process.test');

        $file->put('random stuff and things 2!');

        $this->expectException(ProcessFailedException::class);

        $this->zip->compress($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsthrownOnUnzip()
    {
        $process = m::mock(Process::class)->makePartial();
        $this->builder->shouldReceive('build')
                      ->andReturn($process);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_unzip_process.test');

        $file->put('random stuff and things 2!');

        $this->expectException(ProcessFailedException::class);

        $this->zip->decompress($file);
    }

    public function testPassingTheKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'keep_file_zip.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->zip->compress($file, ['keepOldFile' => true]);

        static::assertTrue($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->zip->decompress($compressedFile, ['keepOldFile' => true]);

        static::assertTrue($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingFalseToKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'delete_file_zip.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->zip->compress($file, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->zip->decompress($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }
}
