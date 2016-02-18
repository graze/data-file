<?php

namespace Graze\DataFile\Modify\Transfer;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Modify\Exception\TransferFailedException;
use Graze\DataFile\Node\FileNode;
use League\Flysystem\MountManager;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;

class Transfer implements FileTransferInterface, LoggerAwareInterface
{
    use OptionalLoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function copyTo(FileNode $from, FileNode $to)
    {
        $mountManager = new MountManager([
            'from' => $from->getFilesystem(),
            'to'   => $to->getFilesystem(),
        ]);

        $this->log(LogLevel::INFO, "Copying file from: '{from}', to: '{to}'", [
            'from' => $from,
            'to'   => $to,
        ]);
        if (!$mountManager->copy('from://' . $from->getPath(), 'to://' . $to->getPath())) {
            throw new TransferFailedException($from, $to);
        }

        return $to;
    }

    /**
     * {@inheritdoc}
     */
    public function moveTo(FileNode $from, FileNode $to)
    {
        $mountManager = new MountManager([
            'from' => $from->getFilesystem(),
            'to'   => $to->getFilesystem(),
        ]);

        $this->log(LogLevel::INFO, "Moving file from: '{from}', to: '{to}'", [
            'from' => $from,
            'to'   => $to,
        ]);
        if (!$mountManager->move('from://' . $from->getPath(), 'to://' . $to->getPath())) {
            throw new TransferFailedException($from, $to);
        }

        return $to;
    }
}
