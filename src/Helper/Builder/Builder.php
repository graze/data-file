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

namespace Graze\DataFile\Helper\Builder;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use ReflectionClass;

class Builder implements BuilderInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /**
     * @param string|object $class
     * @param mixed         ...$arguments
     *
     * @return mixed
     */
    public function build($class, ...$arguments)
    {
        if (is_object($class)) {
            return $class;
        }

        if (!class_exists($class)) {
            throw new InvalidArgumentException("Unable to build class: $class it does not exist");
        }

        $reflection = new ReflectionClass($class);
        $this->log(LogLevel::DEBUG, "Building class: {class}", ['class' => $class]);
        $object = $reflection->newInstanceArgs($arguments);

        if ($object instanceof BuilderAwareInterface) {
            $object->setBuilder($this);
        }

        if ($this->logger && ($object instanceof LoggerAwareInterface)) {
            $object->setLogger($this->logger);
        }

        return $object;
    }
}
