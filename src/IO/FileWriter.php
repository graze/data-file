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
use Psr\Log\LoggerAwareInterface;
use Traversable;

class FileWriter implements WriterInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /** @var FileNodeInterface */
    private $file;
    /** @var FormatInterface */
    private $format;
    /** @var StreamWriter */
    private $writer;

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
        $this->file = $file;
        $this->format = $format;

        $stream = null;
        if ($this->file instanceof NodeStreamInterface) {
            $stream = $this->file->getStream('c+b');
        } else {
            throw new InvalidArgumentException(
                "Only files that implement " . NodeStreamInterface::class . "can be written to"
            );
        }

        if (is_null($this->format)
            && $file instanceof FormatAwareInterface
        ) {
            $this->format = $file->getFormat();
        }

        if (is_null($this->format)) {
            throw new InvalidArgumentException("No format could be determined from \$file or \$format");
        }

        $factory = $formatterFactory ?: new FormatterFactory();
        $formatter = $factory->getFormatter($this->format);

        $this->writer = new StreamWriter($stream, $formatter);
    }

    /**
     * Adds multiple items to the file
     *
     * @param Traversable|array $rows a multidimensional array or a Traversable object
     *
     * @throws InvalidArgumentException If the given rows format is invalid
     *
     * @return static
     */
    public function insertAll($rows)
    {
        $this->writer->insertAll($rows);
        return $this;
    }

    /**
     * Adds a single item
     *
     * @param mixed $row an item to insert
     *
     * @return static
     */
    public function insertOne($row)
    {
        $this->writer->insertOne($row);
        return $this;
    }
}
