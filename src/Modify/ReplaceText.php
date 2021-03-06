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

use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ReplaceText implements FileModifierInterface, LoggerAwareInterface, BuilderAwareInterface
{
    use OptionalLoggerTrait;
    use FileProcessTrait;
    use GetOptionTrait;

    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canModify(FileNodeInterface $file)
    {
        return (
            ($file instanceof localFile) &&
            ($file->exists())
        );
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options List of options:
     *                                   -fromText <string|array> Text to be replace
     *                                   -toText <string|array> Text to replace
     *                                   -postifx <string> (Default: replace) Set this to blank to replace inline
     *                                   -keepOldFile <bool> (Default: true)
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $fromText = $this->requireOption('fromText');
        $toText = $this->requireOption('toText');

        unset($options['fromText']);
        unset($options['toText']);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a LocalFile");
        }

        return $this->replaceText($file, $fromText, $toText, $options);
    }

    /**
     * @param LocalFile       $file
     * @param string|string[] $fromText
     * @param string|string[] $toText
     * @param array           $options List of options:
     *                                 -postfix <string> (Default: replace) Set this to blank to replace inline
     *                                 -keepOldFile <bool> (Default: true)
     *
     * @throws InvalidArgumentException
     * @throws ProcessFailedException
     * @return LocalFile
     */
    public function replaceText(LocalFile $file, $fromText, $toText, array $options = [])
    {
        $this->options = $options;

        $postfix = $this->getOption('postfix', 'replace');
        $output = $this->getTargetFile($file, $postfix);

        $replacementString = $this->getReplacementString($fromText, $toText);

        if ($file->getFilename() == $output->getFilename()) {
            $cmd = sprintf(
                "perl -p -i -e '%s' %s",
                $replacementString,
                $file->getPath()
            );
        } else {
            $cmd = sprintf(
                "perl -p -e '%s' %s > %s",
                $replacementString,
                $file->getPath(),
                $output->getPath()
            );
        }

        $this->log(LogLevel::INFO, "Replacing the text: {from} to {to} in file: '{file}'", [
            'from' => json_encode($fromText),
            'to'   => json_encode($toText),
            'file' => $file,
        ]);

        return $this->processFile($file, $output, $cmd, $this->getOption('keepOldFile', true));
    }

    /**
     * @param string|string[] $from
     * @param string|string[] $to
     *
     * @return string
     */
    private function getReplacementString($from, $to)
    {
        if (is_array($from) && is_array($to)) {
            if (count($from) == count($to)) {
                $sedStrings = [];
                $fromSize = count($from);
                for ($i = 0; $i < $fromSize; $i++) {
                    $sedStrings[] = $this->getReplacementCommand($from[$i], $to[$i]);
                }
                return implode(';', $sedStrings);
            } else {
                throw new InvalidArgumentException("Number of items in 'fromText' (" . count($from) . ") is different to 'toText' (" . count($to) . ")");
            }
        } elseif (is_array($from) || is_array($to)) {
            throw new InvalidArgumentException("Only one of fromText or toText is an array, both should be arrays or string");
        } else {
            return $this->getReplacementCommand($from, $to);
        }
    }

    /**
     * Get the string replacement command for a single item
     *
     * @param string $fromText
     * @param string $toText
     *
     * @return string
     */
    private function getReplacementCommand($fromText, $toText)
    {
        $characters = ["'", ";", "\\", "/"];
        $escaped = ["\\'", "\\;", "\\\\", "\\/"];
        return sprintf(
            's/%s/%s/g',
            str_replace($characters, $escaped, $fromText),
            str_replace($characters, $escaped, $toText)
        );
    }
}
