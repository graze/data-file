<?php

namespace Graze\DataFile\Test\Unit\Node\FileSystem;

use Graze\DataFile\Node\FileSystem\FilesystemWrapper;
use Graze\DataFile\Node\FileSystem\FilesystemWrapperInterface;
use Graze\DataFile\Test\TestCase;
use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;
use Mockery as m;

class FilesystemWrapperTest extends TestCase
{
    public function testInstanceOf()
    {
        $filesystem = m::mock(FilesystemInterface::class);
        $wrapper = new FilesystemWrapper($filesystem);

        static::assertInstanceOf(FilesystemWrapperInterface::class, $wrapper);
        static::assertNotInstanceOf(FilesystemWrapperInterface::class, $filesystem);
    }

    public function testGetAdapter()
    {
        $filesystem = m::mock(Filesystem::class);
        $adapter = m::mock(AdapterInterface::class);
        $filesystem->shouldReceive('getAdapter')
                   ->andReturn($adapter);
        $wrapper = new FilesystemWrapper($filesystem);

        static::assertSame($adapter, $wrapper->getAdapter());
    }

    public function testGetAdapterWithANonFileSystemWillThrowAnException()
    {
        $filesystem = m::mock(FileSystemInterface::class);
        $wrapper = new FilesystemWrapper($filesystem);

        static::expectException(InvalidArgumentException::class);
        $wrapper->getAdapter();
    }

    public function testAllMethodsGetPassedToFileSystem()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $wrapper = new FilesystemWrapper($fileSystem);

        $methods = [
            'has'           => ['some/path'],
            'read'          => ['some/path'],
            'readStream'    => ['some/path'],
            'listContents'  => ['some/path', false],
            'getMetadata'   => ['some/path'],
            'getSize'       => ['some/path'],
            'getMimetype'   => ['some/path'],
            'getTimestamp'  => ['some/path'],
            'getVisibility' => ['some/path'],
            'write'         => ['some/path', 'contents', []],
            'writeStream'   => ['some/path', 'resource', []],
            'update'        => ['some/path', 'contents', []],
            'updateStream'  => ['some/path', 'resource', []],
            'rename'        => ['some/path', 'some/newpath'],
            'copy'          => ['some/path', 'some/newpath'],
            'delete'        => ['some/path'],
            'deleteDir'     => ['some/path'],
            'createDir'     => ['some/path', []],
            'setVisibility' => ['some/path', 'public'],
            'put'           => ['some/path', 'contents', []],
            'putStream'     => ['some/path', 'resource', []],
            'readAndDelete' => ['some/path'],
            'get'           => ['some/path', m::mock(Handler::class)],
            'addPlugin'     => [m::mock(PluginInterface::class)],
        ];

        foreach ($methods as $method => $args) {
            $fileSystem->shouldReceive($method)
                       ->withArgs($args)
                       ->once();
            call_user_func_array([$wrapper, $method], $args);
        }
    }
}
