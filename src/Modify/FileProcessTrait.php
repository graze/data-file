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

use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Node\LocalFileNodeInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait FileProcessTrait
{
    use ProcessTrait;

    /**
     * @param LocalFileNodeInterface $file
     * @param LocalFileNodeInterface $output
     * @param string                 $cmd
     * @param bool                   $deleteOld
     *
     * @return LocalFileNodeInterface
     */
    protected function processFile(
        LocalFileNodeInterface $file,
        LocalFileNodeInterface $output,
        $cmd,
        $deleteOld = true
    ) {
        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful() || !$output->exists()) {
            throw new ProcessFailedException($process);
        }

        if ($file->exists() && !$deleteOld) {
            $this->log(LogLevel::DEBUG, "Deleting old file: '{file}'", ['file' => $file]);
            $file->delete();
        }

        return $output;
    }
}
