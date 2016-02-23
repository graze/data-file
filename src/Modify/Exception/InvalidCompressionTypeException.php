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

class InvalidCompressionTypeException extends Exception
{
    public function __construct($compression, $message = '', Exception $previous = null)
    {
        $message = "Unknown compression type: $compression. " . $message;

        parent::__construct($message, 0, $previous);
    }
}
