<?php

namespace Graze\DataFile\Test\Integration\Modify\Compress;

use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\Compress\CompressorInterface;
use Graze\DataFile\Modify\Compress\DeCompressorInterface;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GzipTest extends FileTestCase
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

        $compressedFile = $this->gzip->gzip($file);

        static::assertNotNull($compressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $compressedFile);
        static::assertEquals(static::$dir . 'uncompressed_gz.gz', $compressedFile->getPath());
        static::assertTrue($compressedFile->exists());
        static::assertEquals(CompressionType::GZIP, $compressedFile->getCompression());

        $cmd = "file {$compressedFile->getPath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(1, $result, "File is not compressed as gzip");
    }

    public function testFileGetsDecompressedFromGzip()
    {
        $file = new LocalFile(static::$dir . 'get_decompressed_uncompressed_gz.test');
        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->gzip($file);
        $uncompressedFile = $this->gzip->gunzip($compressedFile);

        static::assertNotNull($uncompressedFile);
        static::assertInstanceOf(FileNodeInterface::class, $uncompressedFile);
        static::assertEquals(static::$dir . 'get_decompressed_uncompressed_gz', $uncompressedFile->getPath());
        static::assertTrue($uncompressedFile->exists());
        static::assertEquals(CompressionType::NONE, $uncompressedFile->getCompression());

        $cmd = "file {$uncompressedFile->getPath()} | grep " . escapeshellarg('\bgzip\b') . " | wc -l";
        $result = exec($cmd);
        static::assertEquals(0, $result, "File should not be compressed");
    }

    public function testCallingGzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gzip.test');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: ' . $file->getPath() . ' does not exist'
        );

        $this->gzip->gzip($file);
    }

    public function testCallingGunzipWithAFileThatDoesNotExistsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'invalid_gunzip.test');

        static::setExpectedException(
            'InvalidArgumentException',
            'The file: ' . $file->getPath() . ' does not exist'
        );

        $this->gzip->gunzip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGzip()
    {
        $process = m::mock(Process::class)->makePartial();
        $processFactory = m::mock(ProcessFactory::class);
        $processFactory->shouldReceive('createProcess')
                       ->andReturn($process);
        $this->gzip->setProcessFactory($processFactory);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_gzip_process.test');

        $file->put('random stuff and things 2!');

        static::setExpectedException(ProcessFailedException::class);

        $this->gzip->gzip($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnGunzip()
    {
        $process = m::mock(Process::class)->makePartial();
        $processFactory = m::mock(ProcessFactory::class);
        $processFactory->shouldReceive('createProcess')
                       ->andReturn($process);
        $this->gzip->setProcessFactory($processFactory);
        $process->shouldReceive('run')->once();
        $process->shouldReceive('isSuccessful')->once()->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('bla');
        $process->shouldReceive('getWorkingDirectory')->andReturn('/something/');
        $process->shouldReceive('isOutputDisabled')->andReturn('true');

        $file = new LocalFile(static::$dir . 'failed_gunzip_process.test');

        $file->put('random stuff and things 2!');

        static::setExpectedException(ProcessFailedException::class);

        $this->gzip->gunzip($file);
    }

    public function testPassingTheKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'keep_file_gz.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->gzip($file, ['keepOldFile' => true]);

        static::assertTrue($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->gunzip($compressedFile, ['keepOldFile' => true]);

        static::assertTrue($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testPassingFalseToKeepOldFileOptionWillKeepTheFile()
    {
        $file = new LocalFile(static::$dir . 'delete_file_gz.test');

        $file->put('random stuff and things!');

        $compressedFile = $this->gzip->gzip($file, ['keepOldFile' => false]);

        static::assertFalse($file->exists());
        static::assertTrue($compressedFile->exists());

        $uncompresssedFile = $this->gzip->gunzip($compressedFile, ['keepOldFile' => false]);

        static::assertFalse($compressedFile->exists());
        static::assertTrue($uncompresssedFile->exists());
    }

    public function testCallingCompressWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);
        $file->shouldReceive('__toString')->andReturn('test\node');

        static::setExpectedException(InvalidArgumentException::class);

        $this->gzip->compress($file);
    }

    public function testCallingDeCompressWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);
        $file->shouldReceive('__toString')->andReturn('test\node');

        static::setExpectedException(InvalidArgumentException::class);

        $this->gzip->decompress($file);
    }
}
