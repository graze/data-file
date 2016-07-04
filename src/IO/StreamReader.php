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

use Graze\DataFile\Format\Parser\ParserInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataNode\IteratorNode;
use Graze\DataNode\IteratorNodeInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerAwareInterface;

class StreamReader extends IteratorNode implements ReaderInterface, LoggerAwareInterface, IteratorNodeInterface
{
    use OptionalLoggerTrait;

    /**
     * @param StreamInterface $stream
     * @param ParserInterface $parser
     */
    public function __construct(StreamInterface $stream, ParserInterface $parser)
    {
        parent::__construct($parser->parse($stream));
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
