<?php

namespace Graze\DataFile\Test\Integration\Modify;

use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use Mockery as m;

class MakeDirectoryTest extends FileTestCase
{
    /**
     * @var MakeDirectory
     */
    private $maker;

    public function setUp()
    {
        $this->maker = new MakeDirectory();
    }

    public function testCanMakeDirectory()
    {
        $file = new LocalFile(static::$dir . 'test/file');

        static::assertFalse(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file);

        static::assertTrue(file_exists($file->getDirectory()));
        static::assertSame($file, $retFile);
    }

    public function testCanMakeDirectoryWithSpecificUMode()
    {
        $file = new LocalFile(static::$dir . 'umode_test/file');

        static::assertFalse(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file, MakeDirectory::VISIBILITY_PUBLIC);

        static::assertEquals(0755, fileperms($file->getDirectory()) & 0777);
        static::assertSame($retFile, $file);
    }

    public function testCanCallMakeDirectoryWithAnExistingFolder()
    {
        $file = new LocalFile(static::$dir . 'no_dir_file');

        static::assertTrue(file_exists($file->getDirectory()));

        $retFile = $this->maker->makeDirectory($file);

        static::assertSame($retFile, $file);
    }
}
