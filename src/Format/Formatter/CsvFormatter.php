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

class CsvFormatter implements FormatterInterface
{
    use RowProcessor;
    use InvokeFormatter;

    /**
     * @var CsvFormatInterface
     */
    private $csvFormat;

    /**
     * @var string[]
     */
    private $escapeChars;

    /**
     * @var string[]
     */
    private $replaceChars;

    /**
     * @param CsvFormatInterface $csvFormat
     */
    public function __construct(CsvFormatInterface $csvFormat)
    {
        $this->csvFormat = $csvFormat;

        if ($this->csvFormat->getEscapeCharacter()) {
            $this->escapeChars = [
                $this->csvFormat->getEscapeCharacter(), // escape escape first so that it doesn't re-escape later on
                $this->csvFormat->getDelimiter(),
                "\n",
                "\r",
                "\t",
            ];
            if ($this->csvFormat->hasQuotes() && !$this->csvFormat->isDoubleQuote()) {
                $this->escapeChars[] = $this->csvFormat->getQuoteCharacter();
            }

            $this->escapeChars = array_unique($this->escapeChars);

            $this->replaceChars = array_map(function ($char) {
                return $this->csvFormat->getEscapeCharacter() . $char;
            }, $this->escapeChars);
        }

        if ($this->csvFormat->hasQuotes() && $this->csvFormat->isDoubleQuote()) {
            $this->escapeChars[] = $this->csvFormat->getQuoteCharacter();
            $this->replaceChars[] = str_repeat($this->csvFormat->getQuoteCharacter(), 2);
        }

        $this->addProcessor(new DateTimeProcessor());
        $this->addProcessor(new BoolProcessor());
        $this->addProcessor(new ObjectToStringProcessor());
    }

    /**
     * @param array $data
     *
     * @return string
     */
    public function format(array $data)
    {
        $data = $this->process($data);

        foreach ($data as &$element) {
            if (is_null($element)) {
                $element = $this->csvFormat->getNullOutput();
            } else {
                $element = $this->csvFormat->getQuoteCharacter() . $this->escape($element) . $this->csvFormat->getQuoteCharacter();
            }
        }

        return implode($this->csvFormat->getDelimiter(), $data);
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
     * Return an initial block if required
     *
     * @return string
     */
    public function getInitialBlock()
    {
        return '';
    }

    /**
     * Get a separator between each row
     *
     * @return string
     */
    public function getRowSeparator()
    {
        return $this->csvFormat->getLineTerminator();
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
