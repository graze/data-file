<?php

namespace Graze\DataFile\Modify;

use Graze\DataFile\Helper\GetOption;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ReplaceText implements FileModifierInterface, LoggerAwareInterface, ProcessFactoryAwareInterface
{
    use OptionalLoggerTrait;
    use ProcessTrait;
    use GetOption;

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
     * @extend Graze\DataFile\Node\File\LocalFile
     *
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
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getPath());
        $outputFileName = $pathInfo['filename'] . $postfix;
        if (isset($pathInfo['extension'])) {
            $outputFileName .= '.' . $pathInfo['extension'];
        }
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = $file->getClone()
                       ->setPath($outputFilePath);

        if (is_array($fromText)) {
            if (is_array($toText) &&
                count($fromText) == count($toText)
            ) {
                $sedStrings = [];
                for ($i = 0; $i < count($fromText); $i++) {
                    $sedStrings[] = $this->getReplacementCommand($fromText[$i], $toText[$i]);
                }
                $replacementString = implode(';', $sedStrings);
            } else {
                throw new InvalidArgumentException("Number of items in 'fromText' (" . count($fromText) . ") is different to 'toText' (" . count($toText) . ")");
            }
        } else {
            $replacementString = $this->getReplacementCommand($fromText, $toText);
        }

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

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFile', true)) {
            $this->log(LogLevel::DEBUG, "Deleting old file: '{file}'", ['file' => $file]);
            $file->delete();
        }

        return $output;
    }

    /**
     * Get the string replacement command for a single item
     *
     * @param $fromText
     * @param $toText
     *
     * @return string
     */
    private function getReplacementCommand($fromText, $toText)
    {
        return sprintf(
            's/%s/%s/g',
            str_replace(["'", ";", "\\"], ["\\'", "\\;", "\\\\"], $fromText),
            str_replace(["'", ";", "\\"], ["\\'", "\\;", "\\\\"], $toText)
        );
    }
}
