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

class Tail implements FileModifierInterface, LoggerAwareInterface, BuilderAwareInterface
{
    use OptionalLoggerTrait;
    use FileProcessTrait;
    use GetOptionTrait;

    /**
     * @var string
     */
    protected $lines;

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
        $lines = $this->requireOption('lines');
        unset($options['lines']);

        if (!($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a LocalFile");
        }

        return $this->tail($file, $lines, $options);
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
    public function tail(LocalFile $file, $lines, array $options = [])
    {
        $this->options = $options;
        $this->lines = $lines;

        $postfix = $this->getOption('postfix', 'tail');
        $output = $this->getTargetFile($file, $postfix);

        $this->log(LogLevel::INFO, "Retrieving the last {lines} from file {file}", [
            'lines' => $this->lines,
            'file'  => $file,
        ]);

        $cmd = sprintf('tail -n %s %s > %s', $this->lines, $file->getPath(), $output->getPath());

        return $this->processFile($file, $output, $cmd, $this->getOption('keepOldFile', true));
    }
}
