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

use CallbackFilterIterator;
use Graze\DataFile\Format\Parser\ParserInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Iterator;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerAwareInterface;

class FileReader implements ReaderInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /** @var StreamInterface */
    private $stream;
    /** @var ParserInterface */
    private $parser;

    /**
     * @param StreamInterface $stream
     * @param ParserInterface $parser
     */
    public function __construct(StreamInterface $stream, ParserInterface $parser)
    {
        $this->stream = $stream;
        $this->parser = $parser;
    }

    /**
     * Create an Iterator based on
     *
     * @param callable $callable
     *
     * @return Iterator
     */
    public function fetch(callable $callable = null)
    {
        $iterator = $this->parser->parse($this->stream);
        if ($callable) {
            $iterator = new CallbackFilterIterator($iterator, $callable);
        }
        return $iterator;
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
        return iterator_to_array($this->fetch($callable));
    }
}
