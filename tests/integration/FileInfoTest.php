<?php

namespace Graze\DataFile\Test\Functional\Info\File;

use Graze\DataFile\FileInfo;
use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Compress\Zip;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use Mockery as m;
use Symfony\Component\Process\Process;

class FileInfoTest extends FileTestCase
{
    /**
     * @var FileInfo
     */
    protected $fileInfo;

    /**
     * @var ProcessFactory|m\MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->fileInfo = new FileInfo();
        $this->fileInfo->setProcessFactory($this->processFactory);
    }

    public function testGetFileEncodingForASCIIFile()
    {
        $asciiFile = (new LocalFile(static::$dir . 'ascii_file.test'))
            ->setEncoding('us-ascii');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        static::assertEquals(
            strtolower($asciiFile->getEncoding()),
            strtolower($this->fileInfo->findEncoding($asciiFile))
        );
    }

    public function testGetFileEncodingForUtf8File()
    {
        $utf8file = (new LocalFile(static::$dir . 'utf8_file.test'))
            ->setEncoding('UTF-8');
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));

        static::assertEquals(
            strtolower($utf8file->getEncoding()),
            strtolower($this->fileInfo->findEncoding($utf8file))
        );
    }

    public function testGetFileCompressionForNonCompressedFile()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file.test');
        $file->put('some random text');

        static::assertEquals(
            $file->getCompression(),
            $this->fileInfo->findCompression($file)
        );
        static::assertEquals(CompressionType::NONE, $file->getCompression());
    }

    public function testGetFileCompressionForGzipFile()
    {
        $file = new LocalFile(static::$dir . 'tobegzipped_file.test');
        $file->put('some random text');
        $gzip = new Gzip();
        $gzipFile = $gzip->gzip($file);

        static::assertEquals(
            $gzipFile->getCompression(),
            $this->fileInfo->findCompression($gzipFile)
        );
        static::assertEquals(CompressionType::GZIP, $gzipFile->getCompression());
    }

    public function testGetFileCompressionForZipFile()
    {
        $file = new LocalFile(static::$dir . 'tobezipped.test');
        $file->put('some random text');
        $zip = new Zip();
        $zipFile = $zip->zip($file);

        static::assertEquals(
            $zipFile->getCompression(),
            $this->fileInfo->findCompression($zipFile)
        );
        static::assertEquals(CompressionType::ZIP, $zipFile->getCompression());
    }

    public function testGetFileEncodingForCompressedFile()
    {
        $utf8file = (new LocalFile(static::$dir . 'utf8_tobegzipped_file.test'))
            ->setEncoding('UTF-8');
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));
        $gzip = new Gzip();
        $gzipFile = $gzip->gzip($utf8file);

        static::assertEquals(
            strtolower($gzipFile->getEncoding()),
            strtolower($this->fileInfo->findEncoding($gzipFile))
        );


        static::assertEquals('utf-8', $this->fileInfo->findEncoding($gzipFile));
        static::assertEquals($utf8file->getEncoding(), $gzipFile->getEncoding());
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        $file->put('random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findEncoding($file);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindCompression()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_find_encoding_process.test');
        $file->put('random stuff and things 2!');

        static::setExpectedException(
            'Symfony\Component\Process\Exception\ProcessFailedException'
        );

        $this->fileInfo->findCompression($file);
    }

    public function testWhenTheProcessReturnsAnUnknownEncodingUnknownTypeIsReturned()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('run');
        $process->shouldReceive('isSuccessful')->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('text/plain; charset=utf-8 compressed-encoding=application/lzop; charset=binary; charset=binary');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        $file->put('random stuff and things 2!');

        static::assertEquals(CompressionType::UNKNOWN, $this->fileInfo->findCompression($file));
    }

    public function testWhenTheProcessReturnsAnUnknownFileNullIsReturned()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('run');
        $process->shouldReceive('isSuccessful')->andReturn(true);
        $process->shouldReceive('getOutput')->andReturn('some random stuff with no charset');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        $file->put('random stuff and things 2!');

        static::assertNull($this->fileInfo->findEncoding($file));
    }
}
