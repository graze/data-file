<?php

namespace Graze\DataFile\Modify\Contract;

use DirectoryIterator;
use Graze\DataFile\Helper\GetOptionTrait;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\FileNode;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MergeFiles implements FileContractorInterface, LoggerAwareInterface, ProcessFactoryAwareInterface
{
    use GetOptionTrait;
    use OptionalLoggerTrait;
    use ProcessTrait;

    /**
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     *
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files, FileNodeInterface $target)
    {
        if (!($target instanceof LocalFile)) {
            return false;
        }

        foreach ($files->getIterator() as $file) {
            if (!($file instanceof LocalFile) ||
                !($file->exists()) ||
                ($file->getCompression() != CompressionType::NONE)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Do the expansion and return a collection
     *
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     * @param array                       $options :keepOldFiles <bool> (Default: true) Keep the old files after merging
     *
     * @return FileNodeInterface
     */
    public function contract(
        FileNodeCollectionInterface $files,
        FileNodeInterface $target,
        array $options = []
    ) {
        $this->options = $options;
        if (!$this->canContract($files, $target)) {
            throw new \InvalidArgumentException("The supplied files are not valid");
        }

        $this->log(LogLevel::INFO, "Merging files in collection $files into: {$target}");

        $filePaths = $files->map(function (LocalFile $item) {
            return $item->getPath();
        });

        $cmd = sprintf(
            'cat %s > %s',
            implode(' ', $filePaths),
            $target->getPath()
        );

        return $this->runCommand($files, $target, $cmd, $this->getOption('keepOldFiles', true));
    }

    /**
     * @param FileNodeCollectionInterface $files
     * @param FileNodeInterface           $target
     * @param string                      $cmd
     * @param bool                        $keepOld
     *
     * @return FileNodeInterface
     * @throws \Graze\DataFile\Modify\Exception\MakeDirectoryFailedException
     */
    private function runCommand(FileNodeCollectionInterface $files, FileNodeInterface $target, $cmd, $keepOld = true)
    {
        if ($target instanceof FileNode) {
            $maker = new MakeDirectory();
            $maker->makeDirectory($target);
        }

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!$keepOld) {
            $this->log(LogLevel::DEBUG, "Deleting old files in collection $files");
            $files->map(function (LocalFile $item) {
                if ($item->exists()) {
                    $item->delete();
                }
                $count = iterator_count(new DirectoryIterator($item->getDirectory()));
                if ($count == 2) {
                    rmdir($item->getDirectory());
                }
            });
        }

        return $target;
    }
}
