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

namespace Graze\DataFile\Format\Parser;

use ArrayIterator;
use Graze\DataFile\Format\JsonFormatInterface;
use Graze\DataFile\Helper\LineStreamIterator;
use Graze\DataFile\Helper\MapIterator;
use Iterator;
use RuntimeException;

class JsonParser implements ParserInterface
{
    const JSON_DEFAULT_DEPTH = 512;

    /** @var JsonFormatInterface */
    private $format;

    /**
     * JsonFormatter constructor.
     *
     * @param JsonFormatInterface $format
     */
    public function __construct(JsonFormatInterface $format)
    {
        $this->format = $format;
    }

    /**
     * @param resource $stream
     *
     * @return Iterator
     */
    public function parse($stream)
    {
        if ($this->format->isEachLine()) {
            $iterator = new LineStreamIterator(
                $stream,
                [
                    LineStreamIterator::OPTION_ENDING         => "\n",
                    LineStreamIterator::OPTION_IGNORE_BLANK   => $this->format->isIgnoreBlankLines(),
                    LineStreamIterator::OPTION_INCLUDE_ENDING => false,
                ]
            );
            $iterator->setIgnoreBlank($this->format->isIgnoreBlankLines());
            return new MapIterator($iterator, [$this, 'decodeJson']);
        } else {
            $json = $this->decodeJson(stream_get_contents($stream));
            if (is_array($json)) {
                return new ArrayIterator($json);
            } else {
                throw new RuntimeException("Expecting a json array to parse, unknown format detected");
            }
        }
    }

    /**
     * @param string $string
     *
     * @return mixed
     */
    public function decodeJson($string)
    {
        return json_decode(
            $string,
            $this->format->isJsonDecodeAssoc(),
            static::JSON_DEFAULT_DEPTH,
            $this->format->getJsonDecodeOptions()
        );
    }
}
