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

use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Parser;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Graze\DataFile\Format\CsvFormatInterface;
use Iterator;
use LimitIterator;
use Psr\Http\Message\StreamInterface;

class CsvParser implements ParserInterface
{
    /** @var CsvFormatInterface */
    private $csvFormat;

    /**
     * @param CsvFormatInterface $csvFormat
     */
    public function __construct(CsvFormatInterface $csvFormat)
    {
        $this->csvFormat = $csvFormat;
    }

    /**
     * @param StreamInterface $stream
     *
     * @return Iterator
     */
    public function parse(StreamInterface $stream)
    {
        $configuration = new CsvConfiguration([
            CsvConfiguration::OPTION_DELIMITER    => $this->csvFormat->getDelimiter(),
            CsvConfiguration::OPTION_QUOTE        => $this->csvFormat->getQuoteCharacter(),
            CsvConfiguration::OPTION_ESCAPE       => $this->csvFormat->getEscapeCharacter(),
            CsvConfiguration::OPTION_DOUBLE_QUOTE => $this->csvFormat->isDoubleQuote(),
            CsvConfiguration::OPTION_NEW_LINE     => $this->csvFormat->getLineTerminator(),
            CsvConfiguration::OPTION_NULL         => $this->csvFormat->getNullOutput(),
        ]);
        $tokeniser = new StreamTokeniser($configuration, $stream);
        $parser = new Parser();
        return $this->parseIterator(
            $parser->parse($tokeniser->getTokens())
        );
    }

    /**
     * Parse a supplied iterator
     *
     * @param Iterator $iterator
     *
     * @return Iterator
     */
    private function parseIterator(Iterator $iterator)
    {
        if ($this->csvFormat->hasHeaders() || $this->csvFormat->getLimit() !== -1) {
            $iterator = new LimitIterator($iterator, $this->csvFormat->getHeaders(), $this->csvFormat->getLimit());
        }
        return $iterator;
    }
}
