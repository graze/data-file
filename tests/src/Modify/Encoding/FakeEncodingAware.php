<?php

namespace Graze\DataFile\Test\Modify\Encoding;

use Graze\DataFile\Modify\Encoding\EncodingAwareInterface;
use Graze\DataFile\Modify\Encoding\EncodingAwareTrait;

class FakeEncodingAware implements EncodingAwareInterface
{
    use EncodingAwareTrait;
}
