<?php

namespace Graze\DataFile\Test\Integration\Modify;

use Graze\DataFile\Modify\Encoding\ConvertEncoding;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ConvertEncodingTest extends FileTestCase
{
    /**
     * @var ConvertEncoding
     */
    protected $converter;

    public function setUp()
    {
        $this->converter = new ConvertEncoding();
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock(LocalFile::class);
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->converter->canModify($localFile));
        static::assertFalse(
            $this->converter->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock(FileNodeInterface::class);

        static::assertFalse($this->converter->canModify($randomThing));
    }

    public function testModifyWillCheckIfItCanModifyFirst()
    {
        $randomThing = m::mock(FileNodeInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->converter->modify($randomThing);
    }

    public function testChangeEncodingCanConvertBetweenASCIIAndUtf8()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_file.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $isAscii = exec("file {$asciiFile->getPath()} | grep -i " . escapeshellarg('\bascii\b') . " | wc -l");
        static::assertEquals(1, $isAscii, "Original file to convert is not ascii");

        $newFile = $this->converter->toEncoding($asciiFile, 'UTF-8');

        $isUTF8 = exec("file {$newFile->getPath()} | grep -i " . escapeshellarg('\UTF-8\b') . " | wc -l");
        static::assertEquals(1, $isUTF8, "Converted file should be UTF8");
    }

    public function testCallingModifyWillConvertTheEncoding()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_file_modify.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $isAscii = exec("file {$asciiFile->getPath()} | grep -i " . escapeshellarg('\bascii\b') . " | wc -l");
        static::assertEquals(1, $isAscii, "Original file to convert is not ascii");

        $newFile = $this->converter->modify($asciiFile, ['encoding' => 'UTF-8']);

        $isUTF8 = exec("file {$newFile->getPath()} | grep -i " . escapeshellarg('\UTF-8\b') . " | wc -l");
        static::assertEquals(1, $isUTF8, "Converted file should be UTF8");
    }

    public function testChangeEncodingCanConvertBetweenUTF8AndUTF16()
    {
        $utf8file = new LocalFile(static::$dir . 'utf8_file.test');
        $utf8file->setEncoding('UTF-8');
        $utf8file->put(mb_convert_encoding('Some random Text €±§', 'UTF-8'));

        $isUtf8 = exec("file {$utf8file->getPath()} | grep -i " . escapeshellarg('\butf-8\b') . " | wc -l");
        static::assertEquals(1, $isUtf8, "Original file to convert is not utf-8");

        $newFile = $this->converter->toEncoding($utf8file, 'UTF-16');

        $isUTF16 = exec("file {$newFile->getPath()} | grep -i " . escapeshellarg('\UTF-16\b') . " | wc -l");
        static::assertEquals(1, $isUTF16, "Converted file should be UTF16");
    }

    public function testSpecifyingThePostfixWillUseThatForTheFile()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_posfix.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $newFile = $this->converter->toEncoding($asciiFile, 'UTF-8', ['postfix' => 'test']);

        static::assertEquals(
            'ascii_posfix-test.test',
            $newFile->getFilename(),
            "Resulting file should have the postfix 'test'"
        );
    }

    public function testSettingKeepOldToTrueFileWillKeepTheFile()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_keep.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $this->converter->toEncoding($asciiFile, 'UTF-8', ['keepOldFile' => true]);

        static::assertTrue($asciiFile->exists(), "The original file should exist");
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheFile()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_delete.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $this->converter->toEncoding($asciiFile, 'UTF-8', ['keepOldFile' => false]);

        static::assertFalse($asciiFile->exists(), "The original file should be deleted");
    }

    public function testConversionWillFailWhenSpecifyingAnInvalidEncoding()
    {
        $asciiFile = new LocalFile(static::$dir . 'ascii_fail.test');
        $asciiFile->put(mb_convert_encoding('Some random Text', 'ASCII'));

        $this->expectException(ProcessFailedException::class);

        $this->converter->toEncoding($asciiFile, 'NotARealEncoding');
    }
}
