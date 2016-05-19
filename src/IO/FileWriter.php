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

use Graze\DataFile\Format\Formatter\FormatterInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Traversable;

class FileWriter implements WriterInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /** @var FormatterInterface */
    private $formatter;
    /** @var StreamInterface */
    private $stream;

    /**
     * @param StreamInterface    $stream
     * @param FormatterInterface $formatter  Optional formatter, if not specified shall be determined based on the
     *                                       format of the file
     */
    public function __construct(StreamInterface $stream, FormatterInterface $formatter)
    {
        $this->stream = $stream;
        $this->formatter = $formatter;
    }

    /**
     * Adds multiple lines to the CSV document
     *
     * a simple wrapper method around insertOne
     *
     * @param Traversable|array $rows a multidimensional array or a Traversable object
     *
     * @throws InvalidArgumentException If the given rows format is invalid
     *
     * @return static
     */
    public function insertAll($rows)
    {
        $this->log(LogLevel::INFO, 'Writing {count} rows to file', ['count' => count($rows)]);

        $this->initialiseForWriting($this->stream);

        $first = true;
        foreach ($rows as $row) {
            if (!$first) {
                $this->stream->write($this->formatter->getRowSeparator());
            }
            $this->stream->write($this->formatter->format($row));
            $first = false;
        }
        $this->stream->write($this->formatter->getClosingBlock());

        $this->stream->close();

        return $this;
    }

    /**
     * Initialise the resource for writing.
     *
     * If we are at 0, then write initial block, otherwise, remove closing block and add a row separator
     *
     * This is so we can append a file with special characters at the beginning and end
     *
     * @param StreamInterface $stream
     */
    private function initialiseForWriting(StreamInterface $stream)
    {
        // move to the end of the file to always append
        $stream->seek(0, SEEK_END);

        if ($stream->tell() === 0) {
            $stream->write($this->formatter->getInitialBlock());
        } elseif (strlen($this->formatter->getClosingBlock()) > 0) {
            $endBlock = $this->formatter->getClosingBlock();
            $stream->seek(strlen($endBlock) * -1, SEEK_CUR);
            $stream->write($this->formatter->getRowSeparator());
        } elseif (strlen($this->formatter->getRowSeparator()) > 0) {
            $stream->write($this->formatter->getRowSeparator());
        }
    }

    /**
     * Adds a single line
     *
     * @param mixed $row an item to insert
     *
     * @return static
     */
    public function insertOne($row)
    {
        return $this->insertAll([$row]);
    }
}
