<?php

namespace Graze\DataFile;

use Graze\DataFile\Helper\OptionalLoggerTrait;
use Graze\DataFile\Helper\Process\ProcessFactoryAwareInterface;
use Graze\DataFile\Helper\Process\ProcessTrait;
use Graze\DataFile\Modify\Compress\CompressionFactory;
use Graze\DataFile\Node\LocalFileNodeInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FileInfo implements ProcessFactoryAwareInterface, LoggerAwareInterface
{
    use ProcessTrait;
    use OptionalLoggerTrait;

    /**
     * Find the Encoding of a specified file
     *
     * @param LocalFileNodeInterface $file
     *
     * @return null|string
     * @throws ProcessFailedException
     */
    public function findEncoding(LocalFileNodeInterface $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $result = $process->getOutput();
        if (preg_match('/charset=([^\s]+)/i', $result, $matches)) {
            $this->log(LogLevel::DEBUG, "Found the encoding for '{file}' as '{encoding}'", [
                'file'     => $file,
                'encoding' => $matches[1],
            ]);
            return $matches[1];
        }
        return null;
    }

    /**
     * Find the compression of a specified file
     *
     * @param LocalFileNodeInterface $file
     *
     * @return string|null
     */
    public function findCompression(LocalFileNodeInterface $file)
    {
        $cmd = "file --brief --uncompress --mime {$file->getPath()}";

        $process = $this->getProcess($cmd);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $compressionFactory = new CompressionFactory();

        $result = $process->getOutput();
        if (preg_match('/compressed-encoding=application\/(?:x-)?(.+?);/i', $result, $matches)) {
            if ($compressionFactory->isCompression($matches[1])) {
                $this->log(LogLevel::DEBUG, "Found the compression for '{file}' as '{compression}'", [
                    'file'        => $file,
                    'compression' => $matches[1],
                ]);
                return $matches[1];
            }
            return CompressionFactory::TYPE_UNKNOWN;
        }
        return CompressionFactory::TYPE_NONE;
    }
}
