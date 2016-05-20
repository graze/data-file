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
    /** @var string|bool */
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
        if ($this->readStream()) {
            $this->position++;
        }
    }

    /**
     * @param string $ending
     *
     * @return string
     */
    private function readStreamTill($ending)
    {
        $buffer = '';
        $len = strlen($ending);
        do {
            $char = $this->stream->read(1);
            $buffer .= $char;
        } while ($char && substr($buffer, $len * -1) != $ending && !$this->stream->eof());
        return $buffer;
    }

    /**
     * @return bool
     */
    private function readStream()
    {
        if ($this->stream->eof()) {
            $this->current = false;
            return false;
        } else {
            $buffer = $this->readStreamTill($this->ending);

            if ($this->isBlankBuffer($buffer)) {
                return $this->readStream();
            } else {
                $this->stripEnding($buffer);
                $this->current = $buffer;
                return true;
            }
        }
    }

    /**
     * @param string $buffer Reference as we want speed here!
     *
     * @return bool
     */
    private function isBlankBuffer(&$buffer)
    {
        return (($buffer == '' || $buffer == $this->ending) && $this->ignoreBlank);
    }

    /**
     * @param string $buffer Reference as we want speed here!
     */
    private function stripEnding(&$buffer)
    {
        if (!$this->includeEnding && substr($buffer, strlen($this->ending) * -1) == $this->ending) {
            array_splice($buffer, $len * -1);
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
        $this->readStream();
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
