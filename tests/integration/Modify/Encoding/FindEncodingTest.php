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

namespace Graze\DataFile\Test\Integration\Modify\Encoding;

use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Encoding\FindEncoding;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class FindEncodingTest extends FileTestCase
{
    /**
     * @var FindEncoding
     */
    protected $findEncoding;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->findEncoding = new FindEncoding();
        $this->findEncoding->setProcessFactory($this->processFactory);
    }

    public function testGetFileEncodingForASCIIFile()
    {
        $asciiFile = (new LocalFile(static::$dir . 'ascii_file.test'))
            ->setEncoding('us-ascii');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        static::assertEquals(
            strtolower($asciiFile->getEncoding()),
            strtolower($this->findEncoding->getEncoding($asciiFile))
        );
    }

    public function testGetFileEncodingForUtf8File()
    {
        $utf8file = (new LocalFile(static::$dir . 'utf8_file.test'))
            ->setEncoding('UTF-8');
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));

        static::assertEquals(
            strtolower($utf8file->getEncoding()),
            strtolower($this->findEncoding->getEncoding($utf8file))
        );
    }

    public function testGetFileEncodingForCompressedFile()
    {
        $utf8file = (new LocalFile(static::$dir . 'utf8_tobegzipped_file.test'))
            ->setEncoding('UTF-8');
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));
        $gzip = new Gzip();
        $gzipFile = $gzip->compress($utf8file);

        static::assertEquals(
            strtolower($gzipFile->getEncoding()),
            strtolower($this->findEncoding->getEncoding($gzipFile))
        );

        static::assertEquals('utf-8', $this->findEncoding->getEncoding($gzipFile));
        static::assertEquals($utf8file->getEncoding(), $gzipFile->getEncoding());
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
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

        $this->findEncoding->getEncoding($file);
    }

    public function testWhenTheProcessReturnsAnUnknownFileNullIsReturned()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('mustRun');
        $process->shouldReceive('getOutput')->andReturn('some random stuff with no charset');

        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'unknown_compression.test');
        $file->put('random stuff and things 2!');

        static::assertNull($this->findEncoding->getEncoding($file));
    }

    public function testCanModifyCanModifyLocalFiles()
    {
        $file = new LocalFile(static::$dir . 'uncompressed_file.test');
        $file->put('some random text');

        static::assertTrue($this->findEncoding->canModify($file));
    }

    public function testUnableToModifyNonLocalFiles()
    {
        $file = m::mock(FileNodeInterface::class);
        static::assertFalse($this->findEncoding->canModify($file));

        $this->expectException(InvalidArgumentException::class);
        $this->findEncoding->modify($file);
    }

    public function testModifyWillSetTheEncoding()
    {
        $utf8file = (new LocalFile(static::$dir . 'utf8_file.test'));
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));

        $utf8file = $this->findEncoding->modify($utf8file);
        static::assertEquals('utf-8', $utf8file->getEncoding());
    }
}
