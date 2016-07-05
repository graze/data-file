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

namespace Graze\DataFile\Node;

use Graze\DataFile\Modify\Compress\CompressionAwareInterface;
use Graze\DataFile\Modify\Encoding\EncodingAwareInterface;

interface LocalFileNodeInterface extends FileNodeInterface, CompressionAwareInterface, EncodingAwareInterface
{
}
