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
use Graze\DataFile\Format\Parser\ParserFactory;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\NodeStreamInterface;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;
use Iterator;
use Psr\Log\LoggerAwareInterface;

class FileReader implements ReaderInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /** @var FileNodeInterface */
    private $file;
    /** @var FormatInterface */
    private $format;
    /** @var StreamReader */
    private $reader;

    /**
     * FileReader constructor.
     *
     * @param FileNodeInterface           $file
     * @param FormatInterface|null        $format
     * @param ParserFactoryInterface|null $parserFactory
     */
    public function __construct(
        FileNodeInterface $file,
        FormatInterface $format = null,
        ParserFactoryInterface $parserFactory = null
    ) {
        $this->file = $file;
        $this->format = $format;

        if ($this->file instanceof NodeStreamInterface) {
            $stream = $this->file->getStream('r');
        } else {
            $stream = new Stream($this->file->readStream());
        }

        if (is_null($this->format)
            && $file instanceof FormatAwareInterface
        ) {
            $this->format = $file->getFormat();
        }

        if (is_null($this->format)) {
            throw new InvalidArgumentException("No format could be determined from \$file or \$format");
        }

        $factory = $parserFactory ?: new ParserFactory();
        $parser = $factory->getParser($this->format);

        $this->reader = new StreamReader($stream, $parser);
    }

    /**
     * Fetch the next row from a result set
     *
     * @param callable|null $callable a callable function to be applied to each Iterator item
     *
     * @return Iterator
     */
    public function fetch(callable $callable = null)
    {
        return $this->reader->fetch($callable);
    }

    /**
     * Returns a sequential array of all items
     *
     * The callable function will be applied to each Iterator item
     *
     * @param callable|null $callable a callable function
     *
     * @return array
     */
    public function fetchAll(callable $callable = null)
    {
        return $this->reader->fetchAll($callable);
    }
}
