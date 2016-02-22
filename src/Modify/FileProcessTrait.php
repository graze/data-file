<?php

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
