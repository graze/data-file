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
use Graze\DataFile\Format\Formatter\FormatterFactoryInterface;
use Graze\DataFile\Format\Parser\ParserFactory;
use Graze\DataFile\Format\Parser\ParserFactoryInterface;
use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\Builder\BuilderInterface;
use Graze\DataFile\Helper\FileHelper;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\IO\FileReader;
use Graze\DataFile\IO\FileWriter;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFileNodeInterface;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class ReFormat implements FileModifierInterface, LoggerAwareInterface, BuilderAwareInterface
{
    use OptionalLoggerTrait;
    use GetOptionTrait;
    use FileProcessTrait;
    use FileHelper;

    /** @var FormatterFactoryInterface */
    private $formatterFactory;
    /** @var ParserFactoryInterface */
    private $parserFactory;

    /**
     * ReFormat constructor.
     *
     * @param FormatterFactoryInterface|null $formatterFactory
     * @param ParserFactoryInterface|null    $parserFactory
     * @param BuilderInterface|null          $builder
     */
    public function __construct(
        FormatterFactoryInterface $formatterFactory = null,
        ParserFactoryInterface $parserFactory = null,
        BuilderInterface $builder = null
    ) {
        $this->builder = $builder;
        $this->formatterFactory = $formatterFactory ?: $this->getBuilder()->build(FormatterFactory::class);
        $this->parserFactory = $parserFactory ?: $this->getBuilder()->build(ParserFactory::class);
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
     * @param array             $options -output <LocalFileNodeInterface> Output file to write to
     *                                   -format <FormatInterface> Format to use for the output
     *                                   -postfix <string> (Default: 'format') string to use when creating a copied
     *                                   field (if applicable)
     *                                   -keepOldFile <bool> (Default: true) keep or delete the input file
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;

        $format = $this->getOption('format', null);
        $output = $this->getOption('output', null);
        if ((is_null($format) || (!$format instanceof FormatInterface))
            && (is_null($output) || (!$output instanceof LocalFileNodeInterface))
        ) {
            throw new InvalidArgumentException("Missing a Required option: 'format' or 'output'");
        }

        return $this->reFormat($file, $format, $output, null, $options);
    }

    /**
     * @param FileNodeInterface      $file
     * @param FormatInterface|null   $outputFormat
     * @param FileNodeInterface|null $output
     * @param FormatInterface|null   $inputFormat
     * @param array                  $options -postfix <string> (Default: 'format') string to use when creating a
     *                                        copied field (if applicable)
     *                                        -keepOldFile <bool> (Default: true) keep or delete the input file
     *
     * @return FileNodeInterface
     */
    public function reFormat(
        FileNodeInterface $file,
        FormatInterface $outputFormat = null,
        FileNodeInterface $output = null,
        FormatInterface $inputFormat = null,
        array $options = []
    ) {
        $this->options = $options;

        if (is_null($output)) {
            if ($file instanceof LocalFileNodeInterface) {
                $output = $this->getTargetFile($file, $this->getOption('postfix', 'format'));
            } else {
                $output = $this->getTemporaryFile();
            }
        }

        if ($output instanceof FormatAwareInterface
            && $outputFormat != null
        ) {
            $output->setFormat($outputFormat);
        }

        /** @var FileReader $reader */
        $reader = $this->getBuilder()->build(FileReader::class, $file, $inputFormat, $this->parserFactory);
        /** @var FileWriter $writer */
        $writer = $this->getBuilder()->build(FileWriter::class, $output, $outputFormat, $this->formatterFactory);

        foreach ($reader->fetch() as $row) {
            $writer->insertOne($row);
        }

        if ($file->exists() && !$this->getOption('keepOldFile', true)) {
            $this->log(LogLevel::DEBUG, "Deleting old file: '{file}'", ['file' => $file]);
            $file->delete();
        }

        return $output;
    }
}
