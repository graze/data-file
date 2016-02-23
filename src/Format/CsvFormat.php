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

namespace Graze\DataFile\Format;

use Graze\DataFile\Helper\GetOptionTrait;

class CsvFormat implements CsvFormatInterface
{
    use GetOptionTrait;

    const DEFAULT_DELIMITER       = ',';
    const DEFAULT_NULL_OUTPUT     = '\\N';
    const DEFAULT_INCLUDE_HEADERS = true;
    const DEFAULT_LINE_TERMINATOR = "\n";
    const DEFAULT_QUOTE_CHARACTER = '"';

    const OPTION_DELIMITER       = 'delimiter';
    const OPTION_NULL_OUTPUT     = 'nullOutput';
    const OPTION_INCLUDE_HEADERS = 'includeHeaders';
    const OPTION_LINE_TERMINATOR = 'lineTerminator';
    const OPTION_QUOTE_CHARACTER = 'quoteCharacter';

    /**
     * @var string
     */
    protected $delimiter;

    /**
     * @var string
     */
    protected $quoteCharacter;

    /**
     * @var string
     */
    protected $nullOutput;

    /**
     * @var bool
     */
    protected $includeHeaders;

    /**
     * @var string
     */
    protected $lineTerminator;

    /**
     * @param array $options -delimiter <string> (Default: ,) Character to use between fields
     *                       -quoteCharacter <string> (Default: ")
     *                       -nullOutput <string> (Default: \N)
     *                       -includeHeaders <bool> (Default: true)
     *                       -lineTerminator <string> (Default: \n) [Not current implemented]
     */
    public function __construct($options = [])
    {
        $this->options = $options;
        $this->delimiter = $this->getOption(static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quoteCharacter = $this->getOption(static::OPTION_QUOTE_CHARACTER, static::DEFAULT_QUOTE_CHARACTER);
        $this->nullOutput = $this->getOption(static::OPTION_NULL_OUTPUT, static::DEFAULT_NULL_OUTPUT);
        $this->includeHeaders = $this->getOption(static::OPTION_INCLUDE_HEADERS, static::DEFAULT_INCLUDE_HEADERS);
        $this->lineTerminator = $this->getOption(static::OPTION_LINE_TERMINATOR, static::DEFAULT_LINE_TERMINATOR);
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param string $delimiter
     *
     * @return CsvFormat
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasQuotes()
    {
        return $this->quoteCharacter <> '';
    }

    /**
     * @return string
     */
    public function getNullOutput()
    {
        return $this->nullOutput;
    }

    /**
     * @param string $nullOutput
     *
     * @return CsvFormat
     */
    public function setNullOutput($nullOutput)
    {
        $this->nullOutput = $nullOutput;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasHeaders()
    {
        return $this->includeHeaders;
    }

    /**
     * @param bool $includeHeaders
     *
     * @return CsvFormat
     */
    public function setHeaders($includeHeaders)
    {
        $this->includeHeaders = $includeHeaders;
        return $this;
    }

    /**
     * @return string
     */
    public function getLineTerminator()
    {
        return $this->lineTerminator;
    }

    /**
     * @param string $lineTerminator
     *
     * @return CsvFormat
     */
    public function setLineTerminator($lineTerminator)
    {
        $this->lineTerminator = $lineTerminator;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuoteCharacter()
    {
        return $this->quoteCharacter;
    }

    /**
     * @param string $quoteCharacter
     *
     * @return CsvFormat
     */
    public function setQuoteCharacter($quoteCharacter)
    {
        $this->quoteCharacter = $quoteCharacter;
        return $this;
    }

    /**
     * Type type of file format (defined in FileFormatType::)
     *
     * @return string
     */
    public function getType()
    {
        return 'csv';
    }
}
