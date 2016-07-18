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

use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Format\Processor\BoolProcessor;
use Graze\DataFile\Format\Processor\DateTimeProcessor;
use Graze\DataFile\Format\Processor\ObjectToStringProcessor;
use Graze\DataFile\Format\Processor\RowProcessor;
use Traversable;

class CsvFormatter implements FormatterInterface
{
    use RowProcessor;
    use InvokeFormatter;
    use TraversableTrait;

    /** @var CsvFormatInterface */
    private $csvFormat;
    /** @var string[] */
    private $escapeChars;
    /** @var string[] */
    private $replaceChars;
    /** @var string */
    private $initial;
    /** @var bool */
    private $first = true;

    /**
     * @param CsvFormatInterface $csvFormat
     */
    public function __construct(CsvFormatInterface $csvFormat)
    {
        $this->csvFormat = $csvFormat;

        $this->buildReplacements();

        $this->initial = (!is_null($this->csvFormat->getBom())) ? $this->csvFormat->getBom() : '';

        $this->addProcessor(new DateTimeProcessor());
        $this->addProcessor(new BoolProcessor());
        $this->addProcessor(new ObjectToStringProcessor());
    }

    /**
     * Build replacements to perform for each entry
     */
    private function buildReplacements()
    {
        if ($this->csvFormat->getEscape()) {
            $this->escapeChars = [
                $this->csvFormat->getEscape(), // escape escape first so that it doesn't re-escape later on
                $this->csvFormat->getDelimiter(),
                "\n",
                "\r",
                "\t",
            ];
            if ($this->csvFormat->hasQuote() && !$this->csvFormat->useDoubleQuotes()) {
                $this->escapeChars[] = $this->csvFormat->getQuote();
            }

            $this->escapeChars = array_unique($this->escapeChars);

            $this->replaceChars = array_map(function ($char) {
                return $this->csvFormat->getEscape() . $char;
            }, $this->escapeChars);
        }

        if ($this->csvFormat->hasQuote() && $this->csvFormat->useDoubleQuotes()) {
            $this->escapeChars[] = $this->csvFormat->getQuote();
            $this->replaceChars[] = str_repeat($this->csvFormat->getQuote(), 2);
        }
    }

    /**
     * Gets a prefix for headers if required
     *
     * @param array $data
     *
     * @return string
     */
    private function getHeaderPrefix(array $data)
    {
        if ($this->first && $this->csvFormat->hasHeaderRow()) {
            $this->first = false;
            $postHeaderPad = $this->csvFormat->getDataStart() - $this->csvFormat->getHeaderRow();
            return $this->format(array_keys($data)) . str_repeat($this->getRowSeparator(), $postHeaderPad);
        }
        return '';
    }

    /**
     * @param array|Traversable $row
     *
     * @return string
     */
    public function format($row)
    {
        $data = $this->getArray($row);
        $prefix = $this->getHeaderPrefix($data);
        $data = $this->process($data);

        foreach ($data as &$element) {
            if (is_null($element)) {
                $element = $this->csvFormat->getNullValue();
            } else {
                $element = $this->csvFormat->getQuote() . $this->escape($element) . $this->csvFormat->getQuote();
            }
        }

        return $prefix . $this->encode(implode($this->csvFormat->getDelimiter(), $data));
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function escape($string)
    {
        return str_replace($this->escapeChars, $this->replaceChars, $string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function encode($string)
    {
        return mb_convert_encoding($string, $this->csvFormat->getEncoding());
    }

    /**
     * Return an initial block if required
     *
     * @return string
     */
    public function getInitialBlock()
    {
        $linePad = $this->csvFormat->hasHeaderRow() ?
            $this->csvFormat->getHeaderRow() - 1 :
            $this->csvFormat->getDataStart() - 1;

        return $this->initial . str_repeat($this->getRowSeparator(), $linePad);
    }

    /**
     * Get a separator between each row
     *
     * @return string
     */
    public function getRowSeparator()
    {
        return $this->encode($this->csvFormat->getNewLine());
    }

    /**
     * Return a closing block if required
     *
     * @return string
     */
    public function getClosingBlock()
    {
        return '';
    }
}
