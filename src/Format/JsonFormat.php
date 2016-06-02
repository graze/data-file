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

class JsonFormat implements FormatInterface, JsonFormatInterface
{
    use GetOptionTrait;

    const OPTION_FILE_TYPE          = 'fileType';
    const OPTION_ENCODE_OPTIONS     = 'encodeOptions';
    const OPTION_DECODE_OPTIONS     = 'decodeOptions';
    const OPTION_DECODE_ASSOC       = 'decodeAssoc';
    const OPTION_IGNORE_BLANK_LINES = 'ignoreBlankLines';

    const DEFAULT_TYPE               = JsonFormatInterface::JSON_FILE_TYPE_SINGLE_BLOCK;
    const DEFAULT_ENCODE_OPTIONS     = 0;
    const DEFAULT_DECODE_OPTIONS     = 0;
    const DEFAULT_DECODE_ASSOC       = false;
    const DEFAULT_IGNORE_BLANK_LINES = true;

    /**
     * The type of the file
     *
     * @var int
     */
    private $fileType = JsonFormatInterface::JSON_FILE_TYPE_SINGLE_BLOCK;
    /** @var int */
    private $encodeOptions = 0;
    /** @var int */
    private $decodeOptions = 0;
    /** @var bool */
    private $decodeToAssoc = false;
    /** @var bool */
    private $ignoreBlankLines;

    /**
     * @param array $options -fileType <int> (Default: JsonFormat::JSON_FILE_TYPE_SINGLE_BLOCK) a
     *                       JsonFormat::JSON_FILE_TYPE_*
     *                       -encodeOptions <int> Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP,
     *                       JSON_HEX_APOS, JSON_PRETTY_PRINT, JSON_NUMERIC_CHECK, JSON_UNESCAPED_SLASHES,
     *                       JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE,
     *                       JSON_PARTIAL_OUTPUT_ON_ERROR. The behaviour of these constants is described on the JSON
     *                       constants page.
     *                       -decodeOptions <int> Bitmask of JSON decode options. Currently only
     *                       JSON_BIGINT_AS_STRING
     *                       is supported (default is to cast large integers as floats)
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
        $this->fileType = $this->getOption(static::OPTION_FILE_TYPE, static::DEFAULT_TYPE);
        $this->encodeOptions = $this->getOption(static::OPTION_ENCODE_OPTIONS, static::DEFAULT_ENCODE_OPTIONS);
        $this->decodeOptions = $this->getOption(static::OPTION_DECODE_OPTIONS, static::DEFAULT_DECODE_OPTIONS);
        $this->decodeToAssoc = $this->getOption(static::OPTION_DECODE_ASSOC, static::DEFAULT_DECODE_ASSOC);
        $this->ignoreBlankLines = $this->getOption(
            static::OPTION_IGNORE_BLANK_LINES,
            static::DEFAULT_IGNORE_BLANK_LINES
        );
    }

    /**
     * Type type of file format
     *
     * @return string
     */
    public function getType()
    {
        return 'json';
    }

    /**
     * Each line is an individual json blob
     *
     * @return bool
     */
    public function isEachLine()
    {
        return $this->fileType === JsonFormatInterface::JSON_FILE_TYPE_EACH_LINE;
    }

    /**
     * The whole file is a single json blob
     *
     * @return bool
     */
    public function isSingleBlock()
    {
        return $this->fileType === JsonFormatInterface::JSON_FILE_TYPE_SINGLE_BLOCK;
    }

    /**
     * @param int $fileType
     *
     * @return $this
     */
    public function setJsonFileType($fileType)
    {
        $this->fileType = $fileType;
        return $this;
    }

    /**
     * Get the type of json file ::JSON_FILE_TYPE_*
     *
     * @return int static::JSON_FILE_TYPE_*
     */
    public function getJsonFileType()
    {
        return $this->fileType;
    }

    /**
     * Set the encoding options.
     *
     * @param int $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS,
     *                     JSON_PRETTY_PRINT, JSON_NUMERIC_CHECK, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT,
     *                     JSON_PRESERVE_ZERO_FRACTION, JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR. The
     *                     behaviour of these constants is described on the JSON constants page.
     *
     * @link http://php.net/manual/en/json.constants.php
     *
     * @return $this
     */
    public function setJsonEncodeOptions($options)
    {
        $this->encodeOptions = $options;
        return $this;
    }

    /**
     * Get the Json Encode Options
     *
     * Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK,
     * JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT, JSON_PRESERVE_ZERO_FRACTION,
     * JSON_UNESCAPED_UNICODE, JSON_PARTIAL_OUTPUT_ON_ERROR.
     *
     * The behaviour of these constants is described on the JSON constants page.
     *
     * @link http://php.net/manual/en/json.constants.php
     *
     * @return int
     */
    public function getJsonEncodeOptions()
    {
        return $this->encodeOptions;
    }

    /**
     * @param int $options Bitmask of JSON decode options. Currently only
     *                     JSON_BIGINT_AS_STRING
     *                     is supported (default is to cast large integers as floats)
     *
     * @return $this
     */
    public function setJsonDecodeOptions($options)
    {
        $this->decodeOptions = $options;
        return $this;
    }

    /**
     * Bitmask of JSON decode options. Currently only
     * JSON_BIGINT_AS_STRING
     * is supported (default is to cast large integers as floats)
     *
     * @return mixed
     */
    public function getJsonDecodeOptions()
    {
        return $this->decodeOptions;
    }

    /**
     * @param bool $assoc
     *
     * @return $this
     */
    public function setJsonDecodeAssoc($assoc)
    {
        $this->decodeToAssoc = $assoc;
        return $this;
    }

    /**
     * @return bool
     */
    public function isJsonDecodeAssoc()
    {
        return $this->decodeToAssoc;
    }

    /**
     * @param bool $ignore
     *
     * @return $this
     */
    public function setIgnoreBlankLines($ignore)
    {
        $this->ignoreBlankLines = $ignore;
        return $this;
    }

    /**
     * @return bool
     */
    public function isIgnoreBlankLines()
    {
        return $this->ignoreBlankLines;
    }
}
