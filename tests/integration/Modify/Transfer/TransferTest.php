<?php

namespace Graze\DataFile\Test\Integration\Modify\Transfer;

use Graze\DataFile\Modify\Exception\TransferFailedException;
use Graze\DataFile\Modify\Transfer\FileTransferInterface;
use Graze\DataFile\Modify\Transfer\Transfer;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter;
use Mockery as m;

class TransferTest extends FileTestCase
{
    /**
     * @var FileTransferInterface
     */
    protected $transfer;

    public function setUp()
    {
        $this->transfer = new Transfer();
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FileTransferInterface::class, $this->transfer);
    }

    public function testCopyBetweenFileSystems()
    {
        $fromFile = new LocalFile(static::$dir . 'from_between.text');
        $fromFile->put('Some Text In Here');

        $toFile = new FileNode(new Filesystem(new MemoryAdapter()), 'some_file');

        $this->transfer->copyTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertEquals($fromFile->read(), $toFile->read());
    }

    public function testCopyBetweenSameFileSystem()
    {
        $fromFile = new LocalFile(static::$dir . 'from_same.text');
        $fromFile->put('Some Text In Here');

        $toFile = new LocalFile(static::$dir . 'to_same.text');

        $this->transfer->copyTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertEquals($fromFile->read(), $toFile->read());
    }

    public function testMoveDeletesTheOldFile()
    {
        $fromFile = new LocalFile(static::$dir . 'delete_from.text');
        $fromFile->put('Some Text In Here');

        $toFile = new LocalFile(static::$dir . 'delete_to.text');

        $this->transfer->moveTo($fromFile, $toFile);

        static::assertEquals('Some Text In Here', $toFile->read());
        static::assertFalse($fromFile->exists());
    }

    public function testCopyWhenOriginalFileDoesNotExistThrowsAnException()
    {
        $fromFile = new LocalFile(static::$dir . 'fail_from.text');

        $toFile = new LocalFile(static::$dir . 'fail_to.text');

        $this->expectException(FileNotFoundException::class);

        $this->transfer->copyTo($fromFile, $toFile);
    }

    public function testMoveWhenOriginalFileDoesNotExistThrowsAnException()
    {
        $fromFile = new LocalFile(static::$dir . 'fail_move_from.text');

        $toFile = new LocalFile(static::$dir . 'fail_move_to.text');

        $this->expectException(FileNotFoundException::class);

        $this->transfer->moveTo($fromFile, $toFile);
    }

    public function testCopyWhenFilesystemDoesNotReadStreamThrowsAnException()
    {
        $filesystem = m::mock('League\Flysystem\FileSystemInterface')->makePartial();

        $fromFile = new FileNode($filesystem, 'some/file');

        $toFile = new LocalFile(static::$dir . 'fail_copy_file.text');

        $filesystem->shouldReceive('readStream')->with($fromFile->getPath())->andReturn(false);

        $this->expectException(TransferFailedException::class);

        $this->transfer->copyTo($fromFile, $toFile);
    }

    public function testMoveWhenFilesystemDoesNotReadStreamThrowsAnException()
    {
        $filesystem = m::mock('League\Flysystem\FileSystemInterface')->makePartial();

        $fromFile = new FileNode($filesystem, 'some/file');

        $toFile = new LocalFile(static::$dir . 'fail_move_file.text');

        $filesystem->shouldReceive('readStream')->with($fromFile->getPath())->andReturn(false);

        $this->expectException(TransferFailedException::class);

        $this->transfer->moveTo($fromFile, $toFile);
    }
}
