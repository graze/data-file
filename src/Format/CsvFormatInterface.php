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
     * @return CsvFormatInterface
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
     * @return CsvFormatInterface
     */
    public function setQuoteCharacter($quoteCharacter);

    /**
     * @return string
     */
    public function getNullOutput();

    /**
     * @param string $nullOutput
     *
     * @return CsvFormatInterface
     */
    public function setNullOutput($nullOutput);

    /**
     * @return bool
     */
    public function hasHeaders();

    /**
     * @param bool $includeHeaders
     *
     * @return CsvFormatInterface
     */
    public function setHeaders($includeHeaders);

    /**
     * @return string
     */
    public function getLineTerminator();

    /**
     * @param string $lineTerminator
     *
     * @return CsvFormatInterface
     */
    public function setLineTerminator($lineTerminator);
}
