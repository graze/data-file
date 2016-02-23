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

namespace Graze\DataFile\Test\Unit\Finder;

use Graze\ArrayFilter\AllOfFilter;
use Graze\ArrayFilter\ArrayFilterInterface;
use Graze\DataFile\Finder\FileFinderInterface;
use Graze\DataFile\Finder\MetadataFinder;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFile\Test\TestCase;
use Mockery as m;

class MetadataFinderTest extends TestCase
{
    public function testInstanceOf()
    {
        $finder = new MetadataFinder(new AllOfFilter());
        static::assertInstanceOf(FileFinderInterface::class, $finder);
    }

    public function testFindFilesWithNoFiltersWillReturnAllFiles()
    {
        $file = m::mock(FileNode::class);
        $file->shouldReceive('getMetadata')
             ->andReturn([
                 'name' => 'test',
             ]);
        $collection = new FileNodeCollection();
        $collection->add($file);

        $filter = m::mock(ArrayFilterInterface::class);
        $filter->shouldReceive('matches')->andReturn(true);
        $finder = new MetadataFinder($filter);

        static::assertEquals($collection->getAll(), $finder->findFiles($collection)->getAll());
    }

    public function testFindFilesWithWithBasicFiltersWillReturnMatchingFilesOnly()
    {
        $file = m::mock(FileNode::class);
        $file->shouldReceive('getMetadata')
             ->andReturn([
                 'name' => 'test',
             ]);
        $file2 = m::mock(FileNode::class);
        $file2->shouldReceive('getMetadata')
              ->andReturn([
                  'name' => 'test2',
              ]);
        $collection = new FileNodeCollection();
        $collection->add($file);
        $collection->add($file2);

        $filter = m::mock(ArrayFilterInterface::class);
        $filter->shouldReceive('matches')->with(['name' => 'test'])->andReturn(true);
        $filter->shouldReceive('matches')->with(['name' => 'test2'])->andReturn(false);
        $finder = new MetadataFinder($filter);

        $found = $finder->findFiles($collection);
        static::assertCount(1, $found->getAll());
        static::assertEquals([$file], $found->getAll());
    }

    public function testFindFilesReturningFalseWillNotIncludeTheFileInTheResults()
    {
        $file = m::mock(FileNode::class);
        $file->shouldReceive('getMetadata')
             ->andReturn(false);
        $collection = new FileNodeCollection();
        $collection->add($file);

        $finder = new MetadataFinder(new AllOfFilter());

        static::assertEquals(0, $finder->findFiles($collection)->count());
    }
}
