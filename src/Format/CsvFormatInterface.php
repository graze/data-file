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

interface CsvFormatInterface extends FormatInterface
{
    /**
     * @return string
     */
    public function getDelimiter();

    /**
     * @param string $delimiter
     *
     * @return static
     */
    public function setDelimiter($delimiter);

    /**
     * @return bool
     */
    public function hasQuotes();

    /**
     * @return string
     */
    public function getQuoteCharacter();

    /**
     * @param string $quoteCharacter
     *
     * @return static
     */
    public function setQuoteCharacter($quoteCharacter);

    /**
     * @return string
     */
    public function getNullOutput();

    /**
     * @param string $nullOutput
     *
     * @return static
     */
    public function setNullOutput($nullOutput);

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
     * @return string
     */
    public function getLineTerminator();

    /**
     * @param string $lineTerminator
     *
     * @return static
     */
    public function setLineTerminator($lineTerminator);

    /**
     * @return string
     */
    public function getEscapeCharacter();

    /**
     * @param string $escape
     *
     * @return static
     */
    public function setEscapeCharacter($escape);

    /**
     * @return bool
     */
    public function hasEscapeCharacter();

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
     * @return bool
     */
    public function isDoubleQuote();

    /**
     * @param bool $doubleQuote
     *
     * @return static
     */
    public function setDoubleQuote($doubleQuote);
}
