<?php

namespace Graze\DataFile\Test\Integration\Modify\Contract;

use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\Contract\FileContractorInterface;
use Graze\DataFile\Modify\Contract\MergeFiles;
use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MergeFilesTest extends FileTestCase
{
    /**
     * @var ProcessFactory|m\MockInterface
     */
    protected $processFactory;

    /**
     * @var MergeFiles
     */
    private $merge;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->merge = new MergeFiles();
        $this->merge->setProcessFactory($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FileContractorInterface::class, $this->merge);
    }

    public function testCanContractAcceptsFileNodeCollectionInterface()
    {
        $collection = m::mock(FileNodeCollectionInterface::class);
        $collection->shouldReceive('getIterator')
                   ->andReturn([]);

        $node = m::mock(FileNodeInterface::class);
        $merger = new MergeFiles($this->processFactory, $node);
        static::assertTrue($merger->canContract($collection));
    }

    public function testCanContractOnlyAcceptsLocalFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canContract($collection));

        $file2 = m::mock(FileNodeInterface::class);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection));
    }

    public function testCanContractOnlyWithFilesThatExist()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canContract($collection));

        $file2 = m::mock(LocalFile::class);
        $file2->shouldReceive('exists')
              ->andReturn(false);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection));
    }

    public function testCanContractOnlyUncompressedFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionType::NONE);
        $collection->add($file1);

        static::assertTrue($this->merge->canContract($collection));

        $file2 = m::mock(LocalFile::class);
        $file2->shouldReceive('exists')
              ->andReturn(true);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionType::GZIP);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection));
    }

    public function testCallingContractWithAFileThatCannotBeContractedWillThrowAnException()
    {
        $collection = new FileNodeCollection();
        $file = m::mock(LocalFile::class);
        $file->shouldReceive('exists')
             ->andReturn(false);
        $file->shouldReceive('getCompression')
             ->andReturn(CompressionType::NONE);
        $collection->add($file);

        $target = m::mock(LocalFile::class);

        $this->expectException(InvalidArgumentException::class);

        $this->merge->contract($collection, $target);
    }

    public function testCallingContractWithANonLocalTargetWillThrowAnException()
    {
        $collection = new FileNodeCollection();
        $file = m::mock(LocalFile::class);
        $file->shouldReceive('exists')
             ->andReturn(true);
        $file->shouldReceive('getCompression')
             ->andReturn(CompressionType::NONE);
        $collection->add($file);

        $target = m::mock(FileNodeInterface::class);

        $this->expectException(InvalidArgumentException::class);

        $this->merge->contract($collection, $target);
    }

    public function testSimpleMergeFiles()
    {
        $collection = $this->createCollection('simple.merge/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.output');

        $file = $this->merge->contract($collection, $outputFile);

        static::assertSame($file, $outputFile);
        static::assertEquals(
            [
                "File 1 Line 1",
                "File 1 Line 2",
                "File 1 Line 3",
                "File 2 Line 1",
                "File 2 Line 2",
                "File 2 Line 3",
                "File 3 Line 1",
                "File 3 Line 2",
                "File 3 Line 3",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(3, $exists);
    }

    /**
     * @param string $rootDir
     * @param int    $numFiles
     *
     * @return FileNodeCollectionInterface
     */
    private function createCollection($rootDir, $numFiles)
    {
        $mkDir = new MakeDirectory();
        $collection = new FileNodeCollection();
        for ($i = 1; $i <= $numFiles; $i++) {
            $file = new LocalFile(static::$dir . $rootDir . 'part_' . $i);
            $mkDir->makeDirectory($file);
            $file->put("File $i Line 1\nFile $i Line 2\nFile $i Line 3\n");
            $collection->add($file);
        }
        return $collection;
    }

    public function testCallingMergeWithKeepOldFilesAsFalseDeletesAllTheFilesInTheCollection()
    {
        $collection = $this->createCollection('simple.merge.delete/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.delete.output');

        $file = $this->merge->contract($collection, $outputFile, ['keepOldFiles' => false]);

        static::assertSame($file, $outputFile);
        static::assertEquals(
            [
                "File 1 Line 1",
                "File 1 Line 2",
                "File 1 Line 3",
                "File 2 Line 1",
                "File 2 Line 2",
                "File 2 Line 3",
                "File 3 Line 1",
                "File 3 Line 2",
                "File 3 Line 3",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(0, $exists);
    }

    public function testProcessFailedThrowException()
    {
        $process = m::mock('Symfony\Component\Process\Process')->makePartial();
        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $process->shouldReceive('isSuccessful')->andReturn(false);

        // set exception as no guarantee process will run on local system
        $this->expectException(ProcessFailedException::class);

        $collection = $this->createCollection('simple.merge/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.output');

        $this->merge->contract($collection, $outputFile);
    }

    public function testCallingContractWillPassThroughOptions()
    {
        $collection = $this->createCollection('simple.contract.pass.through/', 3);
        $outputFile = new LocalFile(static::$dir . 'simple.contract.pass.through.output');

        $file = $this->merge->contract(
            $collection,
            $outputFile,
            [
                'keepOldFiles' => true,
            ]
        );

        static::assertEquals(
            [
                "File 1 Line 1",
                "File 1 Line 2",
                "File 1 Line 3",
                "File 2 Line 1",
                "File 2 Line 2",
                "File 2 Line 3",
                "File 3 Line 1",
                "File 3 Line 2",
                "File 3 Line 3",
            ],
            $file->getContents()
        );

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(3, $exists);
    }

    public function testDeleteOldFilesWillDeleteAnyEmptyDirectories()
    {
        $collection = $this->createCollection('simple.merge.delete.folder/', 3);

        $outputFile = new LocalFile(static::$dir . 'simple.merge.delete.output');

        $file = $this->merge->contract($collection, $outputFile, ['keepOldFiles' => false]);

        static::assertSame($file, $outputFile);
        static::assertEquals(
            [
                "File 1 Line 1",
                "File 1 Line 2",
                "File 1 Line 3",
                "File 2 Line 1",
                "File 2 Line 2",
                "File 2 Line 3",
                "File 3 Line 1",
                "File 3 Line 2",
                "File 3 Line 3",
            ],
            $file->getContents()
        );

        static::assertFalse(file_exists($collection->getAll()[0]->getDirectory()));

        $exists = $collection->filter(function (FileNodeInterface $item) {
            return $item->exists();
        });

        static::assertCount(0, $exists);
        static::assertTrue($file->exists(), 'output file should exist');
    }
}
