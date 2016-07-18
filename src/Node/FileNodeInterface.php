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

namespace Graze\DataFile\Node;

use Graze\DataFile\Node\FileSystem\FilesystemWrapperInterface;
use Graze\DataNode\NodeInterface;

interface FileNodeInterface extends NodeInterface
{
    /**
     * @return string
     */
    public function getDirectory();

    /**
     * @return string
     */
    public function getPath();

    /**
     * @return string
     */
    public function getFilename();

    /**
     * Returns the contents of the file as an array.
     *
     * @return array
     */
    public function getContents();

    /**
     * Read the file.
     *
     * @return string file contents
     */
    public function read();

    /**
     * Read the file as a stream.
     *
     * @return resource file stream
     */
    public function readStream();

    /**
     * Write the new file.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function write($content);

    /**
     * Write the new file using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function writeStream($resource);

    /**
     * Update the file contents.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function update($content);

    /**
     * Update the file contents with a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function updateStream($resource);

    /**
     * Create the file or update if exists.
     *
     * @param string $content
     *
     * @return bool success boolean
     */
    public function put($content);

    /**
     * Create the file or update if exists using a stream.
     *
     * @param resource $resource
     *
     * @return bool success boolean
     */
    public function putStream($resource);

    /**
     * @return bool
     */
    public function exists();

    /**
     * @return bool
     */
    public function delete();

    /**
     * @return FilesystemWrapperInterface
     */
    public function getFilesystem();
}
