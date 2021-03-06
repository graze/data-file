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

namespace Graze\DataFile\Modify\Encoding;

use Graze\DataFile\Helper\Builder\BuilderAwareInterface;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Modify\FileModifierInterface;
use Graze\DataFile\Modify\FileProcessTrait;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use InvalidArgumentException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

/**
 * Convert the Encoding of a file
 *
 * For a list of the supported encodings run:
 *
 * ```bash
 * iconv -l
 * ```
 */
class ConvertEncoding implements FileModifierInterface, LoggerAwareInterface, BuilderAwareInterface
{
    use OptionalLoggerTrait;
    use FileProcessTrait;
    use GetOptionTrait;

    /**
     * @extend Graze\DataFile\Node\File\LocalFile Only apply to local files
     *
     * @param LocalFile $file
     * @param string    $encoding             Encoding as defined by iconv
     * @param array     $options              -postfix <string> (Default: toEncoding)
     *                                        -keepOldFile <bool> (Default: true)
     *
     * @return LocalFile
     */
    public function toEncoding(LocalFile $file, $encoding, array $options = [])
    {
        $this->options = $options;

        $postfix = $this->getOption('postfix', $encoding);
        $output = $this->getTargetFile($file, $postfix)
                       ->setEncoding($encoding);

        $cmd = "iconv " .
            ($file->getEncoding() ? "-f {$file->getEncoding()} " : '') .
            "-t {$encoding} " .
            "{$file->getPath()} " .
            "> {$output->getPath()}";

        $this->log(LogLevel::INFO, "Converting file: '{file}' encoding to '{encoding}'", [
            'file'     => $file,
            'encoding' => $encoding,
        ]);

        return $this->processFile($file, $output, $cmd, $this->getOption('keepOldFile', true));
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
     *                                   -encoding <string>
     *                                   -postfix <string> (Default: toEncoding) Set this to blank to replace inline
     *                                   -keepOldFile <bool> (Default: true)
     *
     * @return FileNodeInterface
     */
    public function modify(FileNodeInterface $file, array $options = [])
    {
        if (!$this->canModify($file) || !($file instanceof LocalFile)) {
            throw new InvalidArgumentException("Supplied: $file is not a valid LocalFile");
        }

        $this->options = $options;
        $encoding = $this->requireOption('encoding');
        unset($options['encoding']);

        return $this->toEncoding($file, $encoding, $options);
    }
}
