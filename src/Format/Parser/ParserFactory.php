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

namespace Graze\DataFile\Format\Parser;

use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\JsonFormatInterface;
use InvalidArgumentException;

class ParserFactory implements ParserFactoryInterface
{
    /**
     * @param FormatInterface $format
     *
     * @return ParserInterface
     */
    public function getParser(FormatInterface $format)
    {
        switch ($format->getType()) {
            case 'csv':
                if ($format instanceof CsvFormatInterface) {
                    return new CsvParser($format);
                } else {
                    throw new InvalidArgumentException(
                        "Format indicates it is csv but does not implement CsvFormatInterface"
                    );
                }

            // fallthrough
            case 'json':
                if ($format instanceof JsonFormatInterface) {
                    return new JsonParser($format);
                } else {
                    throw new InvalidArgumentexception(
                        "Format indicates it is json but does not implement JsonFormatInterface"
                    );
                }

            // fallthrough
            default:
                throw new InvalidArgumentException("Supplied format: {$format->getType()} is unknown");
        }
    }
}
