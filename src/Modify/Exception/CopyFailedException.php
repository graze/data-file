<?php

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
