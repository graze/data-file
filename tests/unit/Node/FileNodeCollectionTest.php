<?php

namespace Graze\DataFile\Test\Unit\Node;

use Graze\DataFile\Node\FileNodeCollection;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Test\TestCase;
use Graze\DataNode\NodeCollection;
use Graze\DataNode\NodeInterface;
use InvalidArgumentException;
use Mockery as m;

class FileNodeCollectionTest extends TestCase
{
    /**
     * @var FileNodeCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->collection = new FileNodeCollection();
    }

    public function testIsDataNodeCollection()
    {
        static::assertInstanceOf(NodeCollection::class, $this->collection);
    }

    public function testGetCommonPrefixReturnsCommonPrefixOfFiles()
    {
        $file1 = m::mock(FileNodeInterface::class);
        $file1->shouldReceive('getPath')->andReturn('some/common/path/to/file1.txt');
        $file2 = m::mock(FileNodeInterface::class);
        $file2->shouldReceive('getPath')->andReturn('some/common/path/to/file2.txt');
        $file3 = m::mock(FileNodeInterface::class);
        $file3->shouldReceive('getPath')->andReturn('some/common/path/to/file3.txt');

        $this->collection->add($file1);
        $this->collection->add($file2);
        $this->collection->add($file3);

        static::assertEquals('some/common/path/to/file', $this->collection->getCommonPrefix());
    }

    public function testGetCommonPrefixReturnsNullIfThereIsNoCommonPrefix()
    {
        $file1 = m::mock(FileNodeInterface::class);
        $file1->shouldReceive('getPath')->andReturn('some/common/path/to/file1.txt');
        $file2 = m::mock(FileNodeInterface::class);
        $file2->shouldReceive('getPath')->andReturn('some/common/path/to/file2.txt');
        $file3 = m::mock(FileNodeInterface::class);
        $file3->shouldReceive('getPath')->andReturn('other/nonCommon/path/to/file3.txt');

        $this->collection->add($file1);
        $this->collection->add($file2);
        $this->collection->add($file3);

        static::assertNull($this->collection->getCommonPrefix());
    }

    public function testGetCommonPrefixReturnsNullIfThereAreNoItems()
    {
        static::assertNull($this->collection->getCommonPrefix());
    }

    public function testCanAddAFileNode()
    {
        $node = m::mock(FileNodeInterface::class);
        static::assertSame($this->collection, $this->collection->add($node));
    }

    public function testAddingANonDataNodeWillThrowAnException()
    {
        $node = m::mock(NodeInterface::class);

        static::setExpectedException(InvalidArgumentException::class);

        $this->collection->add($node);
    }
}
