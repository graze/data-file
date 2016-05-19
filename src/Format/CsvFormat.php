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
    const DEFAULT_HEADERS         = 1;
    const DEFAULT_LINE_TERMINATOR = "\n";
    const DEFAULT_QUOTE_CHARACTER = '"';
    const DEFAULT_ESCAPE          = '\\';
    const DEFAULT_LIMIT           = -1;
    const DEFAULT_DOUBLE_QUOTE    = false;

    const OPTION_DELIMITER       = 'delimiter';
    const OPTION_NULL_OUTPUT     = 'nullOutput';
    const OPTION_HEADERS         = 'headers';
    const OPTION_LINE_TERMINATOR = 'lineTerminator';
    const OPTION_QUOTE_CHARACTER = 'quoteCharacter';
    const OPTION_ESCAPE          = 'escape';
    const OPTION_LIMIT           = 'limit';
    const OPTION_DOUBLE_QUOTE    = 'doubleQuote';

    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $quoteCharacter;
    /** @var string */
    protected $nullOutput;
    /** @var int */
    protected $headers;
    /** @var string */
    protected $lineTerminator;
    /** @var bool */
    protected $nullQuotes;
    /** @var string */
    protected $escape;
    /** @var int */
    protected $limit;
    /** @var bool */
    protected $doubleQuote;

    /**
     * @param array $options -delimiter <string> (Default: ,) Character to use between fields
     *                       -quoteCharacter <string> (Default: ")
     *                       -nullOutput <string> (Default: \N)
     *                       -headers <int> (Default: 1)
     *                       -lineTerminator <string> (Default: \n)
     *
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->delimiter = $this->getOption(static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quoteCharacter = $this->getOption(static::OPTION_QUOTE_CHARACTER, static::DEFAULT_QUOTE_CHARACTER);
        $this->nullOutput = $this->getOption(static::OPTION_NULL_OUTPUT, static::DEFAULT_NULL_OUTPUT);
        $this->headers = $this->getOption(static::OPTION_HEADERS, static::DEFAULT_HEADERS);
        $this->lineTerminator = $this->getOption(static::OPTION_LINE_TERMINATOR, static::DEFAULT_LINE_TERMINATOR);
        $this->escape = $this->getOption(static::OPTION_ESCAPE, static::DEFAULT_ESCAPE);
        $this->limit = $this->getOption(static::OPTION_LIMIT, static::DEFAULT_LIMIT);
        $this->doubleQuote = $this->getOption(static::OPTION_DOUBLE_QUOTE, static::DEFAULT_DOUBLE_QUOTE);
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
     * @return static
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
     * @return static
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
        return $this->headers > 0;
    }

    /**
     * @param int $headers
     *
     * @return static
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeaders()
    {
        return $this->headers;
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
     * @return static
     */
    public function setLineTerminator($lineTerminator)
    {
        $this->lineTerminator = $lineTerminator;
        return $this;
    }

    /**
     * @note Csv Rfc spec defines escaping of quotes to be done using double quotes `""`
     *
     * @return string
     */
    public function getQuoteCharacter()
    {
        return $this->quoteCharacter;
    }

    /**
     * @param string $quoteCharacter
     *
     * @return static
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

    /**
     * @return string
     */
    public function getEscapeCharacter()
    {
        return $this->escape;
    }

    /**
     * @param string $escape
     *
     * @return static
     */
    public function setEscapeCharacter($escape)
    {
        $this->escape = $escape;
        return $this;
    }

    /**
     * Get the limit that should be returned (-1 for no limit)
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the limit of the number of items to be returned (-1 for not limit)
     *
     * @param int $limit
     *
     * @return static
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDoubleQuote()
    {
        return $this->doubleQuote;
    }

    /**
     * @param bool $doubleQuote
     *
     * @return static
     */
    public function setDoubleQuote($doubleQuote)
    {
        $this->doubleQuote = $doubleQuote;
        return $this;
    }
}
