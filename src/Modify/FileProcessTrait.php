<?php

namespace Graze\DataFile\Modify;

use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Node\LocalFile;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

trait FileProcessTrait
{
    use ProcessTrait;

    /**
     * @param LocalFile $file
     * @param LocalFile $output
     * @param string    $cmd
     * @param bool      $deleteOld
     *
     * @return LocalFile
     */
    protected function processFile(LocalFile $file, LocalFile $output, $cmd, $deleteOld = true)
    {
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
