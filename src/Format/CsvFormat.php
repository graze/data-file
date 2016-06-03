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

use Graze\CsvToken\Csv\Bom;
use Graze\DataFile\Helper\GetOptionTrait;

class CsvFormat implements CsvFormatInterface
{
    use GetOptionTrait;

    const DEFAULT_DELIMITER       = ',';
    const DEFAULT_NULL_OUTPUT     = '\\N';
    const DEFAULT_HEADER_ROW      = -1;
    const DEFAULT_DATA_START      = 1;
    const DEFAULT_LINE_TERMINATOR = "\n";
    const DEFAULT_QUOTE_CHARACTER = '"';
    const DEFAULT_ESCAPE          = '\\';
    const DEFAULT_LIMIT           = -1;
    const DEFAULT_DOUBLE_QUOTE    = false;
    const DEFAULT_BOM             = null;
    const DEFAULT_ENCODING        = 'UTF-8';

    const OPTION_DELIMITER       = 'delimiter';
    const OPTION_NULL_OUTPUT     = 'nullOutput';
    const OPTION_HEADER_ROW      = 'headerRow';
    const OPTION_DATA_START      = 'dataStart';
    const OPTION_LINE_TERMINATOR = 'lineTerminator';
    const OPTION_QUOTE_CHARACTER = 'quoteCharacter';
    const OPTION_ESCAPE          = 'escape';
    const OPTION_LIMIT           = 'limit';
    const OPTION_DOUBLE_QUOTE    = 'doubleQuote';
    const OPTION_BOM             = 'bom';
    const OPTION_ENCODING        = 'encoding';

    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $quoteCharacter;
    /** @var string */
    protected $nullOutput;
    /** @var int */
    protected $headerRow;
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
    /** @var int */
    protected $dataStart;
    /** @var string|null */
    protected $bom;
    /** @var string */
    protected $encoding;

    /**
     * @param array $options -delimiter <string> (Default: ,) Character to use between fields
     *                       -quoteCharacter <string> (Default: ")
     *                       -nullOutput <string> (Default: \N)
     *                       -headerRow <int> (Default: -1) -1 for no header row. (1 is the first line of the file)
     *                       -dataStart <int> (Default: 1) The line where the data starts (1 is the first list of the
     *                       file)
     *                       -lineTerminator <string> (Default: \n)
     *                       -escape <string> (Default: \\) Character to use for escaping
     *                       -limit <int> Total number of data rows to return
     *                       -doubleQuote <bool> instances of quote in fields are indicated by a double quote
     *                       -bom <string> (Default: null) Specify a ByteOrderMark for this file
     *                       -encoding <string> (Default: UTF-8) Specify the encoding of the csv file
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->delimiter = $this->getOption(static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quoteCharacter = $this->getOption(static::OPTION_QUOTE_CHARACTER, static::DEFAULT_QUOTE_CHARACTER);
        $this->nullOutput = $this->getOption(static::OPTION_NULL_OUTPUT, static::DEFAULT_NULL_OUTPUT);
        $this->headerRow = $this->getOption(static::OPTION_HEADER_ROW, static::DEFAULT_HEADER_ROW);
        $this->dataStart = $this->getOption(static::OPTION_DATA_START, static::DEFAULT_DATA_START);
        $this->lineTerminator = $this->getOption(static::OPTION_LINE_TERMINATOR, static::DEFAULT_LINE_TERMINATOR);
        $this->escape = $this->getOption(static::OPTION_ESCAPE, static::DEFAULT_ESCAPE);
        $this->limit = $this->getOption(static::OPTION_LIMIT, static::DEFAULT_LIMIT);
        $this->doubleQuote = $this->getOption(static::OPTION_DOUBLE_QUOTE, static::DEFAULT_DOUBLE_QUOTE);
        $this->setBom($this->getOption(static::OPTION_BOM, static::DEFAULT_BOM));
        $this->encoding = $this->getOption(static::OPTION_ENCODING, static::DEFAULT_ENCODING);
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
    public function hasHeaderRow()
    {
        return $this->headerRow > 0;
    }

    /**
     * @param int $headerRow
     *
     * @return static
     */
    public function setHeaderRow($headerRow)
    {
        $this->headerRow = $headerRow;
        return $this;
    }

    /**
     * @return int
     */
    public function getHeaderRow()
    {
        return $this->headerRow;
    }

    /**
     * @param int $row
     *
     * @return static
     */
    public function setDataStart($row)
    {
        $this->dataStart = $row;
        return $this;
    }

    /**
     * @return int
     */
    public function getDataStart()
    {
        if ($this->hasHeaderRow() && $this->getHeaderRow() >= $this->dataStart) {
            return max(1, $this->getHeaderRow() + 1);
        }
        return max(1, $this->dataStart);
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
     * @return bool
     */
    public function hasEscapeCharacter()
    {
        return $this->escape !== '';
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

    /**
     * @return null|string
     */
    public function getBom()
    {
        return $this->bom;
    }

    /**
     * @param null|string $bom
     *
     * @return static
     */
    public function setBom($bom)
    {
        $this->bom = $bom;
        if (!is_null($bom)) {
            Bom::getEncoding($bom);
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getEncoding()
    {
        if (!is_null($this->bom)) {
            return Bom::getEncoding($this->bom);
        }
        return $this->encoding;
    }

    /**
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }
}
