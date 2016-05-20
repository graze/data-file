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
     * @param FileNode $from
     * @param FileNode $to
     *
     * @return FileNode
     * @throws TransferFailedException
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
     * @param FileNode $from
     * @param FileNode $to
     *
     * @return FileNode
     * @throws TransferFailedException
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
