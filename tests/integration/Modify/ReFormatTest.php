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

namespace Graze\DataFile\Test\Integration\Modify;

use Graze\DataFile\Format\CsvFormat;
use Graze\DataFile\Format\JsonFormat;
use Graze\DataFile\Modify\ReFormat;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;

class ReFormatTest extends AbstractFileTestCase
{
    public function testReFormatFromCsvToJson()
    {
        $file = new LocalFile(static::$dir . 'reFormatInput.csv');
        $file->setFormat(new CsvFormat([
            'headerRow' => 1,
        ]));

        $input = <<<CSV
"first","second","third"
"1","cake","monkies"
"2","banana","fish"
CSV;
        $file->write($input);

        $output = new LocalFile(static::$dir . 'reFormatOutput.json');
        $output->setFormat(new JsonFormat([
            'fileType' => JsonFormat::JSON_FILE_TYPE_EACH_LINE,
        ]));

        $reFormat = new ReFormat();

        $reFormat->reFormat($file, null, $output);

        $expected = <<<JSON
{"first":"1","second":"cake","third":"monkies"}
{"first":"2","second":"banana","third":"fish"}
JSON;

        static::assertEquals($expected, $output->read());
    }
}
