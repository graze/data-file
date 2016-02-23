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

namespace Graze\DataFile\Helper;

use Psr\Log\LoggerAwareTrait;

trait OptionalLoggerTrait
{
    use LoggerAwareTrait;

    /**
     * Send a log message only if we have a logger instantiated
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     */
    protected function log($level, $message, array $context = [])
    {
        if ($this->logger) {
            $this->logger->log($level, __CLASS__ . ": " . $message, $context);
        }
    }
}
