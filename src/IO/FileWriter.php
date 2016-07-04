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

namespace Graze\DataFile\IO;

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Formatter\FormatterFactory;
use Graze\DataFile\Format\Formatter\FormatterFactoryInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\NodeStreamInterface;
use InvalidArgumentException;

class FileWriter extends StreamWriter
{
    /**
     * FileReader constructor.
     *
     * @param FileNodeInterface              $file
     * @param FormatInterface|null           $format
     * @param FormatterFactoryInterface|null $formatterFactory
     */
    public function __construct(
        FileNodeInterface $file,
        FormatInterface $format = null,
        FormatterFactoryInterface $formatterFactory = null
    ) {
        if ($file instanceof NodeStreamInterface) {
            $stream = $file->getStream('c+b');
        } else {
            throw new InvalidArgumentException(
                "Only files that implement " . NodeStreamInterface::class . "can be written to"
            );
        }

        if (is_null($format)
            && $file instanceof FormatAwareInterface
        ) {
            $format = $file->getFormat();
        }

        if (is_null($format)) {
            throw new InvalidArgumentException("No format could be determined from \$file or \$format");
        }

        $factory = $formatterFactory ?: new FormatterFactory();
        $formatter = $factory->getFormatter($format);

        parent::__construct($stream, $formatter);
    }
}
