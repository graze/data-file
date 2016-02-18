<?php

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
     * @param Exception         $previous
     */
    public function __construct(FileNodeInterface $file, $message = '', Exception $previous = null)
    {
        $message = "Failed to create directory: '{$file->getDirectory()}'. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
