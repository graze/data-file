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

namespace Graze\DataFile\Modify;

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatInterface;
use Graze\DataFile\Format\Formatter\FormatterFactory;
use Graze\DataFile\Format\Parser\ParserFactory;
use Graze\DataFile\Helper\FileHelper;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\IO\FileReader;
use Graze\DataFile\IO\FileWriter;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFileNodeInterface;
use Psr\Log\LoggerAwareInterface;

class ReFormat implements FileModifierInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;
    use GetOptionTrait;
    use FileProcessTrait;
    use FileHelper;

    /** @var FormatterFactory */
    private $formatterFactory;
    /** @var ParserFactory */
    private $parserFactory;

    /**
     * ReFormat constructor.
     *
     * @param FormatterFactory|null $formatterFactory
     * @param ParserFactory|null    $parserFactory
     */
    public function __construct(FormatterFactory $formatterFactory = null, ParserFactory $parserFactory = null)
    {
        $this->formatterFactory = $formatterFactory ?: new FormatterFactory();
        $this->parserFactory = $parserFactory = new ParserFactory();
    }

    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canModify(FileNodeInterface $file)
    {
        return ($file->exists()
            && $file instanceof FormatAwareInterface
            && $file->getFormat() !== null
            && $this->parserFactory->getParser($file->getFormat()) !== null);
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;

        $format = $this->requireOption('format');

        return $this->reFormat($file, $format);
    }

    /**
     * @param FileNodeInterface $file
     * @param FileNodeInterface $output
     * @param FormatInterface   $inputFormat
     * @param FormatInterface   $outputFormat
     *
     * @return FileNodeInterface
     */
    public function reFormat(
        FileNodeInterface $file,
        FormatInterface $outputFormat = null,
        FileNodeInterface $output = null,
        FormatInterface $inputFormat = null
    ) {
        if (is_null($output)) {
            if ($file instanceof LocalFileNodeInterface) {
                $output = $this->getTargetFile($file, $this->getOption('postfix', 'format'));
            } else {
                $output = $this->getTemporaryFile();
            }
        }

        if (
            $output instanceof FormatAwareInterface
            && $outputFormat != null
        ) {
            $output->setFormat($outputFormat);
        }

        $reader = new FileReader($file, $inputFormat, $this->parserFactory);
        $writer = new FileWriter($output, $outputFormat, $this->formatterFactory);

        foreach ($reader->fetch() as $row) {
            $writer->insertOne($row);
        }

        return $output;
    }
}
