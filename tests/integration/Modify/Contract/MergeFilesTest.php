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

namespace Graze\DataFile\Test\Integration\Modify\Contract;

use Graze\DataFile\Helper\Builder\Builder;
use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Modify\Compress\Gzip;
use Graze\DataFile\Modify\Contract\FileContractorInterface;
use Graze\DataFile\Modify\Contract\MergeFiles;
use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MergeFilesTest extends AbstractFileTestCase
{
    /**
     * @var BuilderInterface|m\MockInterface
     */
    protected $builder;

    /**
     * @var MergeFiles
     */
    private $merge;

    public function setUp()
    {
        $this->builder = m::mock(Builder::class)->makePartial();
        $this->merge = new MergeFiles();
        $this->merge->setBuilder($this->builder);
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

        $node = m::mock(LocalFile::class);
        static::assertTrue($this->merge->canContract($collection, $node));
    }

    public function testCanContractOnlyAcceptsLocalFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file1);

        $out = m::mock(LocalFile::class);

        static::assertTrue($this->merge->canContract($collection, $out));

        $file2 = m::mock(FileNodeInterface::class);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection, $out));
    }

    public function testCanContractOnlyWithFilesThatExist()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file1);

        $out = m::mock(LocalFile::class);

        static::assertTrue($this->merge->canContract($collection, $out));

        $file2 = m::mock(LocalFile::class);
        $file2->shouldReceive('exists')
              ->andReturn(false);
        $file2->shouldReceive('getCompression')
              ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection, $out));
    }

    public function testCanContractOnlyUncompressedFiles()
    {
        $collection = new FileNodeCollection();
        $file1 = m::mock(LocalFile::class);
        $file1->shouldReceive('exists')
              ->andReturn(true);
        $file1->shouldReceive('getCompression')
              ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file1);

        $out = m::mock(LocalFile::class);

        static::assertTrue($this->merge->canContract($collection, $out));

        $file2 = m::mock(LocalFile::class);
        $file2->shouldReceive('exists')
              ->andReturn(true);
        $file2->shouldReceive('getCompression')
              ->andReturn(Gzip::NAME);
        $collection->add($file2);

        static::assertFalse($this->merge->canContract($collection, $out));
    }

    public function testCallingContractWithAFileThatCannotBeContractedWillThrowAnException()
    {
        $collection = new FileNodeCollection();
        $file = m::mock(LocalFile::class);
        $file->shouldReceive('exists')
             ->andReturn(false);
        $file->shouldReceive('getCompression')
             ->andReturn(CompressionFactory::TYPE_NONE);
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
             ->andReturn(CompressionFactory::TYPE_NONE);
        $collection->add($file);

        $target = m::mock(FileNodeInterface::class);

        static::assertFalse($this->merge->canContract($collection, $target));

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
        $this->builder->shouldReceive('build')
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
