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

namespace Graze\DataFile\Format\Formatter;

use Graze\DataFile\Format\JsonFormatInterface;
use Graze\DataFile\Format\Processor\DateTimeProcessor;
use Graze\DataFile\Format\Processor\RowProcessor;
use InvalidArgumentException;
use Traversable;

class JsonFormatter implements FormatterInterface
{
    const JSON_ARRAY_START     = '[';
    const JSON_ARRAY_END       = ']' . PHP_EOL;
    const JSON_ARRAY_SEPARATOR = ',' . PHP_EOL;

    use RowProcessor;
    use InvokeFormatter;

    /**
     * @var JsonFormatInterface
     */
    private $format;

    /**
     * @var string
     */
    private $linePostfix;

    /**
     * @var int
     */
    private $encodeOptions;

    /**
     * JsonFormatter constructor.
     *
     * @param JsonFormatInterface $format
     */
    public function __construct(JsonFormatInterface $format)
    {
        $this->addProcessor(new DateTimeProcessor());
        $this->format = $format;
        $this->linePostfix = $format->isSingleBlock() ? static::JSON_ARRAY_SEPARATOR : PHP_EOL;
        $this->encodeOptions = $format->getJsonEncodeOptions();

        // If the json is a blob on each line, turn off pretty print to ensure it is all on a single line
        if ($format->isEachLine()) {
            $this->encodeOptions &= ~JSON_PRETTY_PRINT;
        }
    }

    /**
     * @param array|Traversable $row
     *
     * @return string
     */
    public function format($row)
    {
        if (!$row instanceof Traversable && !is_array($row)) {
            throw new InvalidArgumentException("The input is not an array or traversable");
        }
        $data = ($row instanceof Traversable) ? iterator_to_array($row, true) : $row;
        $data = $this->process($data);

        return json_encode($data, $this->encodeOptions);
    }

    /**
     * Return an initial block if required
     *
     * @return string
     */
    public function getInitialBlock()
    {
        if ($this->format->isSingleBlock()) {
            return static::JSON_ARRAY_START;
        } else {
            return '';
        }
    }

    /**
     * Return a closing block if required
     *
     * @return string
     */
    public function getClosingBlock()
    {
        if ($this->format->isSingleBlock()) {
            return static::JSON_ARRAY_END;
        } else {
            return '';
        }
    }

    /**
     * Get a separator between each row
     *
     * @return string
     */
    public function getRowSeparator()
    {
        return $this->linePostfix;
    }
}
