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

class CopyFailedException extends Exception
{
    public function __construct(FileNodeInterface $fromFile, $newPath, $message = '', Exception $previous = null)
    {
        $message = "Failed to copy file from: '$fromFile' to '$newPath'. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
