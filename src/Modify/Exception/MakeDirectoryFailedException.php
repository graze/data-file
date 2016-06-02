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

namespace Graze\DataFile\Modify\Exception;

use Exception;
use Graze\DataFile\Node\FileNodeInterface;

class MakeDirectoryFailedException extends Exception
{
    /**
     * MakeDirectoryFailedException constructor.
     *
     * @param FileNodeInterface $file
     * @param string            $message
     * @param Exception|null    $previous
     */
    public function __construct(FileNodeInterface $file, $message = '', Exception $previous = null)
    {
        $message = "Failed to create directory: '{$file->getDirectory()}'. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
