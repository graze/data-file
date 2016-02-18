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

class Head implements FileModifierInterface, LoggerAwareInterface, ProcessFactoryAwareInterface
{
    use OptionalLoggerTrait;
    use ProcessTrait;
    use GetOption;

    /**
     * @var int
     */
    private $lines;

    /**
     * Can this file be modified by this modifier
     *
     * @param FileNodeInterface $file
     *
     * @return bool
     */
    public function canModify(FileNodeInterface $file)
    {
        return (($file instanceof LocalFile) &&
            ($file->exists()));
    }

    /**
     * Modify the file
     *
     * @param FileNodeInterface $file
     * @param array             $options List of options:
     *                                   -lines <string> Number of lines to tail (accepts +/- modifiers)
     *                                   -postfix <string> (Default: replace) Set this to blank to replace inline
     *                                   -keepOldFile <bool> (Default: true)
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        $this->options = $options;
        $lines = $this->getOption('lines', null);

        if (is_null($lines)) {
            throw new InvalidArgumentException("Missing option: 'lines'");
        }

        unset($options['lines']);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a LocalFile");
        }

        return $this->head($file, $lines, $options);
    }

    /**
     * Tail a file
     *
     * @param LocalFile $file
     * @param string    $lines         Number of lines to tail (accepts +/- modifiers)
     * @param array     $options       List of options:
     *                                 -postfix <string> (Default: tail)
     *                                 -keepOldFile <bool> (Default: true)
     *
     * @throws ProcessFailedException
     * @return LocalFile
     */
    public function head(LocalFile $file, $lines, array $options = [])
    {
        $this->options = $options;
        $this->lines = $lines;

        $postfix = $this->getOption('postfix', 'tail');
        if (strlen($postfix) > 0) {
            $postfix = '-' . $postfix;
        }

        $pathInfo = pathinfo($file->getPath());
        $outputFileName = $pathInfo['filename'] . $postfix . '.' . $pathInfo['extension'];
        $outputFilePath = $pathInfo['dirname'] . '/' . $outputFileName;

        $output = $file->getClone()
                       ->setPath($outputFilePath);

        $this->log(LogLevel::INFO, "Retrieving the last {lines} from file {file}", [
            'lines' => $this->lines,
            'file'  => $file,
        ]);

        $cmd = sprintf('head -n %s %s > %s', $this->lines, $file->getPath(), $output->getPath());

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFile', true)) {
            $this->log(LogLevel::DEBUG, "Deleting original file: {file}", ['file' => $file]);
            $file->delete();
        }

        return $output;
    }
}
