<?php

namespace Graze\DataFile\Helper\Process;

interface ProcessFactoryAwareInterface
{
    /**
     * @param ProcessFactory $processFactory
     *
     * @return $this
     */
    public function setProcessFactory(ProcessFactory $processFactory);
}
