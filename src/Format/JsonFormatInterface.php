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

interface JsonFormatInterface
{
    const JSON_FILE_TYPE_EACH_LINE    = 1;
    const JSON_FILE_TYPE_SINGLE_BLOCK = 2;

    /**
     * Each line is an individual json blob
     *
     * @return bool
     */
    public function isEachLine();

    /**
     * The whole file is a single json blob
     *
     * @return bool
     */
    public function isSingleBlock();

    /**
     * @param int $fileType one of static::::JSON_FILE_TYPE_*
     *
     * @return $this
     */
    public function setJsonFileType($fileType);

    /**
     * Get the type of json file ::JSON_FILE_TYPE_*
     *
     * @return int static::JSON_FILE_TYPE_*
     */
    public function getJsonFileType();

    /**
     * @param int $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
     *                     JSON_NUMERIC_CHECK, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT,
     *                     JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR. The
     *                     behaviour of these constants is described on the JSON constants page.
     *
     * @link http://php.net/manual/en/json.constants.php
     *
     * @return $this
     */
    public function setJsonEncodeOptions($options);

    /**
     * Get the Json Encode Options
     *
     * Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK,
     * JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION,
     * JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR.
     *
     * The behaviour of these constants is described on the JSON constants page.
     *
     * @link http://php.net/manual/en/json.constants.php
     *
     * @return int
     */
    public function getJsonEncodeOptions();

    /**
     * @param int $options Bitmask of JSON decode options. Currently only
     *                     JSON_BIGINT_AS_STRING
     *                     is supported (default is to cast large integers as floats)
     *
     * @return $this
     */
    public function setJsonDecodeOptions($options);

    /**
     * Bitmask of JSON decode options. Currently only
     * JSON_BIGINT_AS_STRING
     * is supported (default is to cast large integers as floats)
     *
     * @return mixed
     */
    public function getJsonDecodeOptions();

    /**
     * @param bool $assoc
     *
     * @return $this
     */
    public function setJsonDecodeAssoc($assoc);

    /**
     * @return bool
     */
    public function isJsonDecodeAssoc();

    /**
     * @param bool $ignore
     *
     * @return $this
     */
    public function setIgnoreBlankLines($ignore);

    /**
     * @return bool
     */
    public function isIgnoreBlankLines();
}
