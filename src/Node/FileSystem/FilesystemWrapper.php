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

namespace Graze\DataFile\Node\FileSystem;

use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use League\Flysystem\FileExistsException;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\Handler;
use League\Flysystem\PluginInterface;
use League\Flysystem\RootViolationException;

class FilesystemWrapper implements FilesystemWrapperInterface
{
    /**
     * @var FilesystemInterface
     */
    private $fileSystem;

    /**
     * FilesystemWrapper constructor.
     *
     * @param FilesystemInterface $fileSystem
     */
    public function __construct(FilesystemInterface $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->fileSystem->has($path);
    }

    /**
     * Read a file.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file contents or false on failure.
     */
    public function read($path)
    {
        return $this->fileSystem->read($path);
    }

    /**
     * Retrieves a read-stream for a path.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return resource|false The path resource or false on failure.
     */
    public function readStream($path)
    {
        return $this->fileSystem->readStream($path);
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory The directory to list.
     * @param bool   $recursive Whether to list recursively.
     *
     * @return array A list of file metadata.
     */
    public function listContents($directory = '', $recursive = false)
    {
        return $this->fileSystem->listContents($directory, $recursive);
    }

    /**
     * Get a file's metadata.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return array|false The file metadata or false on failure.
     */
    public function getMetadata($path)
    {
        return $this->fileSystem->getMetadata($path);
    }

    /**
     * Get a file's size.
     *
     * @param string $path The path to the file.
     *
     * @return int|false The file size or false on failure.
     */
    public function getSize($path)
    {
        return $this->fileSystem->getSize($path);
    }

    /**
     * Get a file's mime-type.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file mime-type or false on failure.
     */
    public function getMimetype($path)
    {
        return $this->fileSystem->getMimetype($path);
    }

    /**
     * Get a file's timestamp.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The timestamp or false on failure.
     */
    public function getTimestamp($path)
    {
        return $this->fileSystem->getTimestamp($path);
    }

    /**
     * Get a file's visibility.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The visibility (public|private) or false on failure.
     */
    public function getVisibility($path)
    {
        return $this->fileSystem->getVisibility($path);
    }

    /**
     * Write a new file.
     *
     * @param string $path     The path of the new file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function write($path, $contents, array $config = [])
    {
        return $this->fileSystem->write($path, $contents, $config);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path     The path of the new file.
     * @param resource $resource The file handle.
     * @param array    $config   An optional configuration array.
     *
     * @throws \League\Flysystem\InvalidArgumentException If $resource is not a file handle.
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function writeStream($path, $resource, array $config = [])
    {
        return $this->fileSystem->writeStream($path, $resource, $config);
    }

    /**
     * Update an existing file.
     *
     * @param string $path     The path of the existing file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @throws FileNotFoundException
     *
     * @return bool True on success, false on failure.
     */
    public function update($path, $contents, array $config = [])
    {
        return $this->fileSystem->update($path, $contents, $config);
    }

    /**
     * Update an existing file using a stream.
     *
     * @param string   $path     The path of the existing file.
     * @param resource $resource The file handle.
     * @param array    $config   An optional configuration array.
     *
     * @throws \League\Flysystem\InvalidArgumentException If $resource is not a file handle.
     * @throws FileNotFoundException
     *
     * @return bool True on success, false on failure.
     */
    public function updateStream($path, $resource, array $config = [])
    {
        return $this->fileSystem->updateStream($path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param string $path    Path to the existing file.
     * @param string $newpath The new path of the file.
     *
     * @throws FileExistsException   Thrown if $newpath exists.
     * @throws FileNotFoundException Thrown if $path does not exist.
     *
     * @return bool True on success, false on failure.
     */
    public function rename($path, $newpath)
    {
        return $this->fileSystem->rename($path, $newpath);
    }

    /**
     * Copy a file.
     *
     * @param string $path    Path to the existing file.
     * @param string $newpath The new path of the file.
     *
     * @throws FileExistsException   Thrown if $newpath exists.
     * @throws FileNotFoundException Thrown if $path does not exist.
     *
     * @return bool True on success, false on failure.
     */
    public function copy($path, $newpath)
    {
        return $this->fileSystem->copy($path, $newpath);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool True on success, false on failure.
     */
    public function delete($path)
    {
        return $this->fileSystem->delete($path);
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @throws RootViolationException Thrown if $dirname is empty.
     *
     * @return bool True on success, false on failure.
     */
    public function deleteDir($dirname)
    {
        return $this->fileSystem->deleteDir($dirname);
    }

    /**
     * Create a directory.
     *
     * @param string $dirname The name of the new directory.
     * @param array  $config  An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->fileSystem->createDir($dirname, $config);
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path       The path to the file.
     * @param string $visibility One of 'public' or 'private'.
     *
     * @return bool True on success, false on failure.
     */
    public function setVisibility($path, $visibility)
    {
        return $this->fileSystem->setVisibility($path, $visibility);
    }

    /**
     * Create a file or update if exists.
     *
     * @param string $path     The path to the file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function put($path, $contents, array $config = [])
    {
        return $this->fileSystem->put($path, $contents, $config);
    }

    /**
     * Create a file or update if exists.
     *
     * @param string   $path     The path to the file.
     * @param resource $resource The file handle.
     * @param array    $config   An optional configuration array.
     *
     * @throws \League\Flysystem\InvalidArgumentException Thrown if $resource is not a resource.
     *
     * @return bool True on success, false on failure.
     */
    public function putStream($path, $resource, array $config = [])
    {
        return $this->fileSystem->putStream($path, $resource, $config);
    }

    /**
     * Read and delete a file.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file contents, or false on failure.
     */
    public function readAndDelete($path)
    {
        return $this->fileSystem->readAndDelete($path);
    }

    /**
     * Get a file/directory handler.
     *
     * @param string  $path    The path to the file.
     * @param Handler $handler An optional existing handler to populate.
     *
     * @return Handler Either a file or directory handler.
     */
    public function get($path, Handler $handler = null)
    {
        return $this->fileSystem->get($path, $handler);
    }

    /**
     * Register a plugin.
     *
     * @param PluginInterface $plugin The plugin to register.
     *
     * @return $this
     */
    public function addPlugin(PluginInterface $plugin)
    {
        return $this->fileSystem->addPlugin($plugin);
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        if (!method_exists($this->fileSystem, 'getAdapter')) {
            throw new InvalidArgumentException(
                "This wrapper requires the getAdapter method to exist on the fileSystem"
            );
        }
        return $this->fileSystem->getAdapter();
    }
}
