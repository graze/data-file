<?php

namespace Graze\DataFile\Test\Format;

use Graze\DataFile\Format\FormatAwareInterface;
use Graze\DataFile\Format\FormatAwareTrait;

class FakeFormatAware implements FormatAwareInterface
{
    use FormatAwareTrait;

    public function __clone()
    {
        if ($this->format) {
            $this->format = clone $this->format;
        }
    }
}
