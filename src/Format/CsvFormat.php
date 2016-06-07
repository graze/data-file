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

    const DEFAULT_DELIMITER    = ',';
    const DEFAULT_NULL         = '\\N';
    const DEFAULT_HEADER_ROW   = -1;
    const DEFAULT_DATA_START   = 1;
    const DEFAULT_QUOTE        = '"';
    const DEFAULT_ESCAPE       = '\\';
    const DEFAULT_LIMIT        = -1;
    const DEFAULT_DOUBLE_QUOTE = false;
    const DEFAULT_ENCODING     = 'UTF-8';
    const DEFAULT_NEW_LINE     = "\n";
    const DEFAULT_BOM          = null;

    const OPTION_DELIMITER    = 'delimiter';
    const OPTION_NULL         = 'null';
    const OPTION_HEADER_ROW   = 'headerRow';
    const OPTION_DATA_START   = 'dataStart';
    const OPTION_NEW_LINE     = 'newLine';
    const OPTION_QUOTE        = 'quote';
    const OPTION_ESCAPE       = 'escape';
    const OPTION_LIMIT        = 'limit';
    const OPTION_DOUBLE_QUOTE = 'doubleQuote';
    const OPTION_BOM          = 'bom';
    const OPTION_ENCODING     = 'encoding';

    /** @var string */
    protected $delimiter;
    /** @var string */
    protected $quote;
    /** @var string */
    protected $nullValue;
    /** @var int */
    protected $headerRow;
    /** @var string[] */
    protected $newLines;
    /** @var string */
    protected $escape;
    /** @var int */
    protected $limit;
    /** @var bool */
    protected $doubleQuote;
    /** @var int */
    protected $dataStart;
    /** @var string[]|string|null */
    protected $boms;
    /** @var string */
    protected $encoding;

    /**
     * @param array $options -delimiter <string> (Default: ,) Character to use between fields
     *                       -quoteCharacter <string> (Default: ")
     *                       -nullOutput <string> (Default: \N)
     *                       -headerRow <int> (Default: -1) -1 for no header row. (1 is the first line of the file)
     *                       -dataStart <int> (Default: 1) The line where the data starts (1 is the first list of the
     *                       file)
     *                       -lineTerminator <array> (Default: ["\n","\r","\r\n"])
     *                       -escape <string> (Default: \\) Character to use for escaping
     *                       -limit <int> Total number of data rows to return
     *                       -doubleQuote <bool> instances of quote in fields are indicated by a double quote
     *                       -bom <array> (Default: BOM_ALL) Specify a ByteOrderMark for this file (see Bom::BOM_*)
     *                       -encoding <string> (Default: UTF-8) Specify the encoding of the csv file
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->delimiter = $this->getOption(static::OPTION_DELIMITER, static::DEFAULT_DELIMITER);
        $this->quote = $this->getOption(static::OPTION_QUOTE, static::DEFAULT_QUOTE);
        $this->nullValue = $this->getOption(static::OPTION_NULL, static::DEFAULT_NULL);
        $this->headerRow = $this->getOption(static::OPTION_HEADER_ROW, static::DEFAULT_HEADER_ROW);
        $this->dataStart = $this->getOption(static::OPTION_DATA_START, static::DEFAULT_DATA_START);
        $this->escape = $this->getOption(static::OPTION_ESCAPE, static::DEFAULT_ESCAPE);
        $this->limit = $this->getOption(static::OPTION_LIMIT, static::DEFAULT_LIMIT);
        $this->doubleQuote = $this->getOption(static::OPTION_DOUBLE_QUOTE, static::DEFAULT_DOUBLE_QUOTE);
        $this->encoding = $this->getOption(static::OPTION_ENCODING, static::DEFAULT_ENCODING);
        $this->setBom($this->getOption(static::OPTION_BOM, static::DEFAULT_BOM));
        $this->setNewLine($this->getOption(static::OPTION_NEW_LINE, ["\n", "\r", "\r\n"]));
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
     * @return string
     */
    public function getQuote()
    {
        return $this->quote;
    }

    /**
     * @return bool
     */
    public function hasQuote()
    {
        return $this->quote <> '';
    }

    /**
     * @param string $quote
     *
     * @return static
     */
    public function setQuote($quote)
    {
        $this->quote = $quote;
        return $this;
    }

    /**
     * @return string
     */
    public function getNullValue()
    {
        return $this->nullValue;
    }

    /**
     * @param string $nullValue
     *
     * @return static
     */
    public function setNullValue($nullValue)
    {
        $this->nullValue = $nullValue;
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
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @param string $escape
     *
     * @return static
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasEscape()
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
    public function useDoubleQuotes()
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
     * @return string
     */
    public function getEncoding()
    {
        if (!is_null($this->boms)) {
            $bom = is_array($this->boms) ? reset($this->boms) : $this->boms;
            return Bom::getEncoding($bom);
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

    /**
     * @return string[]
     */
    public function getNewLines()
    {
        return $this->newLines;
    }

    /**
     * @param string|string[] $newLine
     *
     * @return static
     */
    public function setNewLine($newLine)
    {
        $this->newLines = is_array($newLine) ? $newLine : [$newLine];
        return $this;
    }

    /**
     * Get a new line for writing
     *
     * @return string
     */
    public function getNewLine()
    {
        return is_array($this->newLines) ? reset($this->newLines) : $this->newLines;
    }

    /**
     * @param null|string[]|string $bom
     *
     * @return static
     */
    public function setBom($bom)
    {
        $this->boms = $bom;
        if (!is_null($bom)) {
            $testBom = is_array($bom) ? reset($bom) : $bom;
            Bom::getEncoding($testBom);
        }
        return $this;
    }

    /**
     * @return string[]
     */
    public function getBoms()
    {
        if (is_null($this->boms)) {
            return $this->getDefaultBoms();
        } elseif (is_array($this->boms)) {
            return $this->boms;
        } else {
            return [$this->boms];
        }
    }

    /**
     * Get a ByteOrderMark for writing if applicable
     *
     * @return string|null
     */
    public function getBom()
    {
        return is_array($this->boms) ? reset($this->boms) : $this->boms;
    }

    /**
     * @return string[]
     */
    private function getDefaultBoms()
    {
        return [Bom::BOM_UTF8, Bom::BOM_UTF16_BE, Bom::BOM_UTF16_LE, Bom::BOM_UTF32_BE, Bom::BOM_UTF32_LE];
    }
}
