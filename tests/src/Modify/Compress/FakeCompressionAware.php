<?php

namespace Graze\DataFile\Test\Modify\Compress;

use Graze\DataFile\Modify\Compress\CompressionAwareInterface;
use Graze\DataFile\Modify\Compress\CompressionAwareTrait;

class FakeCompressionAware implements CompressionAwareInterface
{
    use CompressionAwareTrait;
}
