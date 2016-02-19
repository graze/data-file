<?php

namespace Graze\DataFile\Modify\Contract;

use DirectoryIterator;
use Graze\DataFile\Helper\GetOption;
use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Modify\Compress\CompressionType;
use Graze\DataFile\Modify\MakeDirectory;
use Graze\DataFile\Node\FileNodeCollectionInterface;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MergeFiles implements FileContractorInterface, LoggerAwareInterface, ProcessFactoryAwareInterface
{
    use GetOption;
    use OptionalLoggerTrait;
    use ProcessTrait;

    /**
     * @param FileNodeCollectionInterface $files
     *
     * @return bool
     */
    public function canContract(FileNodeCollectionInterface $files)
    {
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
     * @param FileNodeInterface           $file
     * @param array                       $options :keepOldFiles <bool> (Default: true) Keep the old files after merging
     *
     * @return FileNodeInterface
     */
    public function contract(
        FileNodeCollectionInterface $files,
        FileNodeInterface $file,
        array $options = []
    ) {
        $this->options = $options;
        if (!$this->canContract($files)) {
            throw new \InvalidArgumentException("The supplied files are not valid");
        }

        $this->log(LogLevel::INFO, "Merging files in collection $files into: {$file}");

        $filePaths = $files->map(function (LocalFile $item) {
            return $item->getPath();
        });

        $cmd = sprintf(
            'cat %s > %s',
            implode(' ', $filePaths),
            $file->getPath()
        );

        $maker = new MakeDirectory();
        $maker->makeDirectory($file);

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!$this->getOption('keepOldFiles', true)) {
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

        return $file;
    }
}
