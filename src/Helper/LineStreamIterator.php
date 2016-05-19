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

use Iterator;
use Psr\Http\Message\StreamInterface;

class LineStreamIterator implements Iterator
{
    use GetOptionTrait;

    const OPTION_ENDING         = 'ending';
    const OPTION_IGNORE_BLANK   = 'ignoreBlank';
    const OPTION_INCLUDE_ENDING = 'includeEnding';

    const DEFAULT_ENDING         = PHP_EOL;
    const DEFAULT_IGNORE_BLANK   = true;
    const DEFAULT_INCLUDE_ENDING = false;

    /** @var string */
    private $ending;
    /** @var int */
    private $position = 0;
    /** @var string */
    private $current;
    /** @var bool */
    private $ignoreBlank = true;
    /** @var StreamInterface */
    private $stream;
    /** @var bool */
    private $includeEnding;

    /**
     * LineStreamIterator constructor.
     *
     * @param StreamInterface $stream
     * @param array           $options
     */
    public function __construct(StreamInterface $stream, array $options = [])
    {
        $this->stream = $stream;
        $this->options = $options;
        $this->ending = $this->getOption(static::OPTION_ENDING, static::DEFAULT_ENDING);
        $this->ignoreBlank = $this->getOption(static::OPTION_IGNORE_BLANK, static::DEFAULT_IGNORE_BLANK);
        $this->includeEnding = $this->getOption(static::OPTION_INCLUDE_ENDING, static::DEFAULT_INCLUDE_ENDING);
    }

    /**
     * @param bool $ignore
     *
     * @return $this
     */
    public function setIgnoreBlank($ignore)
    {
        $this->ignoreBlank = $ignore;
        return $this;
    }

    /**
     * Return the current element
     *
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * Move forward to next element
     *
     * @link  http://php.net/manual/en/iterator.next.php
     * @since 5.0.0
     */
    public function next()
    {
        if ($this->readStreamTill($this->ending)) {
            $this->position++;
        }
    }

    /**
     * @param string $ending
     *
     * @return bool
     */
    private function readStreamTill($ending)
    {
        if ($this->stream->eof()) {
            $this->current = false;
            return false;
        } else {
            $len = strlen($ending);
            $buffer = '';
            do {
                $char = $this->stream->read(1);
                $buffer .= $char;
            } while ($char && substr($buffer, $len * -1) != $ending && !$this->stream->eof());

            if (($buffer == '' || $buffer == $ending) && $this->ignoreBlank) {
                return $this->readStreamTill($ending);
            } else {
                if (!$this->includeEnding && substr($buffer, $len * -1) == $ending) {
                    $buffer = substr($buffer, 0, $len * -1);
                }
                $this->current = $buffer;
                return true;
            }
        }
    }

    /**
     * Return the key of the current element
     *
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Checks if current position is valid
     *
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return bool The return value will be casted to boolean and then evaluated.
     *        Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current !== false;
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        $this->position = 0;
        $this->stream->seek(0);
        $this->readStreamTill($this->ending);
    }

    /**
     * @return bool
     */
    public function isIgnoreBlank()
    {
        return $this->ignoreBlank;
    }

    /**
     * @return bool
     */
    public function isIncludeEnding()
    {
        return $this->includeEnding;
    }

    /**
     * @param bool $includeEnding
     *
     * @return $this
     */
    public function setIncludeEnding($includeEnding)
    {
        $this->includeEnding = $includeEnding;
        return $this;
    }

    /**
     * @param string $ending
     *
     * @return $this
     */
    public function setEnding($ending)
    {
        $this->ending = $ending;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnding()
    {
        return $this->ending;
    }
}