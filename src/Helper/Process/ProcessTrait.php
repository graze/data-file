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

namespace Graze\DataFile\Helper\Process;

use Psr\Log\LogLevel;
use RuntimeException;
use Symfony\Component\Process\Process;

trait ProcessTrait
{
    /**
     * @var ProcessFactory
     */
    private $processFactory;

    /**
     * @param ProcessFactory $processFactory
     *
     * @return $this
     */
    public function setProcessFactory(ProcessFactory $processFactory)
    {
        $this->processFactory = $processFactory;

        return $this;
    }

    /**
     * @return ProcessFactory
     */
    public function getProcessFactory()
    {
        if (!$this->processFactory) {
            $this->processFactory = new ProcessFactory();
        }

        return $this->processFactory;
    }

    /**
     * @param string         $commandline The command line to run
     * @param string|null    $cwd         The working directory or null to use the working dir of the current PHP
     *                                    process
     * @param array|null     $env         The environment variables or null to inherit
     * @param string|null    $input       The input
     * @param int|float|null $timeout     The timeout in seconds or null to disable
     * @param array          $options     An array of options for proc_open
     *
     * @return Process
     * @throws RuntimeException When proc_open is not installed
     */
    public function getProcess(
        $commandline,
        $cwd = null,
        array $env = null,
        $input = null,
        $timeout = 60,
        array $options = array()
    ) {
        $this->log(LogLevel::DEBUG, "Running command: {cmd}", ['cmd' => $commandline]);
        return $this->getProcessFactory()->createProcess($commandline, $cwd, $env, $input, $timeout, $options);
    }

    /**
     * Abstract Log function that might should be handed by the OptionalLoggerTrait or similar
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    abstract protected function log($level, $message, array $context = []);
}
