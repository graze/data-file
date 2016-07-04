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

namespace Graze\DataFile\IO;

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Parser\ParserFactory;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\NodeStreamInterface;
use GuzzleHttp\Psr7\Stream;
use InvalidArgumentException;

class FileReader extends StreamReader
{
    /**
     * FileReader constructor.
     *
     * @param FileNodeInterface           $file
     * @param FormatInterface|null        $format
     * @param ParserFactoryInterface|null $parserFactory
     */
    public function __construct(
        FileNodeInterface $file,
        FormatInterface $format = null,
        ParserFactoryInterface $parserFactory = null
    ) {
        if ($file instanceof NodeStreamInterface) {
            $stream = $file->getStream('r');
        } else {
            $stream = new Stream($file->readStream());
        }

        if (is_null($format)
            && $file instanceof FormatAwareInterface
        ) {
            $format = $file->getFormat();
        }

        if (is_null($format)) {
            throw new InvalidArgumentException("No format could be determined from \$file or \$format");
        }

        $factory = $parserFactory ?: new ParserFactory();
        $parser = $factory->getParser($format);

        parent::__construct($stream, $parser);
    }
}
