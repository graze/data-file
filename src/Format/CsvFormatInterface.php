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

use Graze\CsvToken\Csv\CsvConfigurationInterface;

interface CsvFormatInterface extends FormatInterface, CsvConfigurationInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @return string
     */
    public function getQuote();

    /**
     * @return string
     */
    public function getEscape();

    /**
     * @return string[]
     */
    public function getNewLines();

    /**
     * @return bool
     */
    public function useDoubleQuotes();

    /**
     * @return string
     */
    public function getNullValue();

    /**
     * @return string[]
     */
    public function getBoms();

    /**
     * @return string
     */
    public function getEncoding();

    /**
     * @param string $delimiter
     *
     * @return static
     */
    public function setDelimiter($delimiter);

    /**
     * @return bool
     */
    public function hasQuote();

    /**
     * @param string $quote
     *
     * @return static
     */
    public function setQuote($quote);

    /**
     * @param string $nullValue
     *
     * @return static
     */
    public function setNullValue($nullValue);

    /**
     * @return bool
     */
    public function hasHeaderRow();

    /**
     * @param int $headerRow
     *
     * @return static
     */
    public function setHeaderRow($headerRow);

    /**
     * @return int
     */
    public function getHeaderRow();

    /**
     * @param int $row
     *
     * @return static
     */
    public function setDataStart($row);

    /**
     * @return int
     */
    public function getDataStart();

    /**
     * @param string|string[] $newLine
     *
     * @return static
     */
    public function setNewLine($newLine);

    /**
     * Get a new line for writing
     *
     * @return string
     */
    public function getNewLine();

    /**
     * @param string $escape
     *
     * @return static
     */
    public function setEscape($escape);

    /**
     * @return bool
     */
    public function hasEscape();

    /**
     * Get the limit that should be returned (-1 for no limit)
     *
     * @return int
     */
    public function getLimit();

    /**
     * Set the limit of the number of items to be returned (-1 for not limit)
     *
     * @param int $limit
     *
     * @return static
     */
    public function setLimit($limit);

    /**
     * @param bool $doubleQuote
     *
     * @return static
     */
    public function setDoubleQuote($doubleQuote);

    /**
     * @param string|string[] $bom
     *
     * @return static
     */
    public function setBom($bom);

    /**
     * Get a ByteOrderMark for writing if applicable
     *
     * @return string
     */
    public function getBom();

    /**
     * @param string $encoding
     *
     * @return static
     */
    public function setEncoding($encoding);
}
