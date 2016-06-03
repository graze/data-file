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

use ArrayIterator;
use CallbackFilterIterator;
use Graze\CsvToken\Csv\CsvConfiguration;
use Graze\CsvToken\Parser;
use Graze\CsvToken\Tokeniser\StreamTokeniser;
use Graze\DataFile\Format\CsvFormatInterface;
use Graze\DataFile\Helper\MapIterator;
use Iterator;
use LimitIterator;
use Psr\Http\Message\StreamInterface;
use RuntimeException;

class CsvParser implements ParserInterface
{
    /** @var CsvFormatInterface */
    private $csvFormat;
    /** @var array */
    private $headerRow = [];

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
        $config = [
            CsvConfiguration::OPTION_DELIMITER    => $this->csvFormat->getDelimiter(),
            CsvConfiguration::OPTION_QUOTE        => $this->csvFormat->getQuoteCharacter(),
            CsvConfiguration::OPTION_ESCAPE       => $this->csvFormat->getEscapeCharacter(),
            CsvConfiguration::OPTION_DOUBLE_QUOTE => $this->csvFormat->isDoubleQuote(),
            CsvConfiguration::OPTION_NEW_LINES    => [$this->csvFormat->getLineTerminator()],
            CsvConfiguration::OPTION_NULL         => $this->csvFormat->getNullOutput(),
            CsvConfiguration::OPTION_ENCODING     => $this->csvFormat->getEncoding(),
        ];
        if (!is_null($this->csvFormat->getBom())) {
            $config[CsvConfiguration::OPTION_BOMS] = [$this->csvFormat->getBom()];
        }

        $configuration = new CsvConfiguration($config);
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
        $iterator = $this->parseHeaderRow($iterator);
        if ($this->csvFormat->getDataStart() > 1 || $this->csvFormat->getLimit() !== -1) {
            $iterator = new LimitIterator(
                $iterator,
                max(0, $this->csvFormat->getDataStart() - 1),
                $this->csvFormat->getLimit()
            );
        }
        return $iterator;
    }

    /**
     * @param Iterator $iterator
     *
     * @return Iterator
     */
    private function parseHeaderRow(Iterator $iterator)
    {
        if ($this->csvFormat->hasHeaderRow()) {
            $iterator = new CallbackFilterIterator($iterator, [$this, 'handleHeaderRow']);
            $iterator = new MapIterator($iterator, [$this, 'mapHeaders']);
        }
        return $iterator;
    }

    /**
     * Parse the rows looking for the header row and storing it locally
     *
     * @param ArrayIterator $current
     * @param int           $key
     *
     * @return bool
     */
    public function handleHeaderRow(ArrayIterator $current, $key)
    {
        if ($key == $this->csvFormat->getHeaderRow() - 1) {
            $arr = iterator_to_array($current);
            if (count($arr) > 1 || strlen($arr[0]) > 0) {
                $this->headerRow = $arr;
            }
        }
        return true;
    }

    /**
     * Map any headers found onto each element in the data
     *
     * @param ArrayIterator $current
     *
     * @return ArrayIterator
     */
    public function mapHeaders(ArrayIterator $current)
    {
        if (count($this->headerRow) > 0) {
            if (count($this->headerRow) !== $current->count()) {
                throw new RuntimeException(
                    "The number of entries in: " . implode(',', iterator_to_array($current)) .
                    " does not match the header: " . implode(',', $this->headerRow)
                );
            }
            return new ArrayIterator(array_combine(
                $this->headerRow,
                iterator_to_array($current)
            ));
        }
        return $current;
    }
}
