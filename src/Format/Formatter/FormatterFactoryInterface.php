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

namespace Graze\DataFile\Format\Formatter;

use Graze\DataFile\Format\FormatInterface;

interface FormatterFactoryInterface
{
    /**
     * @param FormatInterface $format
     *
     * @return FormatterInterface
     */
    public function getFormatter(FormatInterface $format);
}
