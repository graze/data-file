<?php

namespace Graze\DataFile\Test\Integration\Modify\Compress;

use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\FindCompression;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Compress\Zip;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FindCompressionTest extends FileTestCase
{
    /**
     * @var FindCompression
     */
    protected $findCompression;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    /**
     * @var CompressionFactory|MockInterface
     */
    protected $compressionFactory;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->compressionFactory = m::mock(CompressionFactory::class);
        $this->compressionFactory->shouldReceive('isCompression')
                                 ->with('gzip')
                                 ->andReturn(true);
        $this->compressionFactory->shouldReceive('isCompression')
                                 ->with('zip')
                                 ->andReturn(true);
        $this->compressionFactory->shouldReceive('isCompression')
                                 ->andReturn(false);
        $this->findCompression = new FindCompression($this->compressionFactory);
        $this->findCompression->setProcessFactory($this->processFactory);
    }

    public function testGetFileCompressionForNonCompressedFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file.test');
        $file->put('some random text');

        static::assertEquals(
            $file->getCompression(),
            $this->findCompression->getCompression($file)
        );
        static::assertEquals(CompressionFactory::TYPE_NONE, $file->getCompression());
    }

    public function testGetFileCompressionForGzipFile()
    {
        $file = new LocalFile(static::$dir . 'tobegzipped_file.test');
        $file->put('some random text');
        $gzip = new Gzip();
        $gzipFile = $gzip->compress($file);

        static::assertEquals(
            $gzipFile->getCompression(),
            $this->findCompression->getCompression($gzipFile)
        );
        static::assertEquals(Gzip::NAME, $gzipFile->getCompression());
    }

    public function testGetFileCompressionForZipFile()
    {
        $file = new LocalFile(static::$dir . 'tobezipped.test');
        $file->put('some random text');
        $zip = new Zip();
        $zipFile = $zip->compress($file);

        static::assertEquals(
            $zipFile->getCompression(),
            $this->findCompression->getCompression($zipFile)
        );
        static::assertEquals(Zip::NAME, $zipFile->getCompression());
    }

    public function testWhenTheProcessReturnsAnUnknownCompressionUnknownTypeIsReturned()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('mustRun');
        $process->shouldReceive('getOutput')->andReturn('text/plain; charset=utf-8 compressed-encoding=application/lzop; charset=binary; charset=binary');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        $file->put('random stuff and things 2!');

        static::assertEquals(CompressionFactory::TYPE_UNKNOWN, $this->findCompression->getCompression($file));
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindCompression()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $process->shouldReceive('getCommandLine')->andReturn('cmd');
        $process->shouldReceive('getExitCode')->andReturn(1);
        $process->shouldReceive('getExitCodeText')->andReturn('failed');
        $process->shouldReceive('getWorkingDirectory')->andReturn('bla');
        $process->shouldReceive('isOutputDisabled')->andReturn(true);
        $process->shouldReceive('mustRun')->andThrow(new ProcessFailedException($process));

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        $file->put('random stuff and things 2!');

        $this->expectException(ProcessFailedException::class);

        $this->findCompression->getCompression($file);
    }

    public function testCanModifyCanModifyLocalFiles()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file.test');
        $file->put('some random text');

        static::assertTrue($this->findCompression->canModify($file));
    }

    public function testUnableToModifyNonLocalFiles()
    {
        $file = m::mock(FileNodeInterface::class);
        static::assertFalse($this->findCompression->canModify($file));

        $this->expectException(InvalidArgumentException::class);
        $this->findCompression->modify($file);
    }

    public function testModifyWillSetTheCompression()
    {
        $file = new LocalFile(static::$dir . 'tobegzipped_file.test');
        $file->put('some random text');
        $gzip = new Gzip();
        $gzipFile = $gzip->compress($file);
        $gzipFile->setCompression(CompressionFactory::TYPE_NONE);

        $gzipFile = $this->findCompression->modify($gzipFile);
        static::assertEquals(Gzip::NAME, $gzipFile->getCompression());
    }
}
