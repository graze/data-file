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

namespace Graze\DataFile\Test;

abstract class AbstractFileTestCase extends TestCase
{
    const TEST_DATA_PATH = '/tmp/data/';

    /**
     * @var string
     */
    protected static $dir;

    public static function setUpBeforeClass()
    {
        static::$dir = self::getTestDir();
    }

    public static function tearDownAfterClass()
    {
        if (is_dir(static::$dir)) {
            self::rmDirRecursive(static::$dir);
        }
    }

    /**
     * Get the directory used for testing file io
     *
     * @return string
     */
    private static function getTestDir()
    {
        date_default_timezone_set('UTC');
        $dir = static::TEST_DATA_PATH . strftime('%Y%m%d-%H%M/');
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }
        return $dir;
    }

    /**
     * Delete the folder and all files/folders within it
     *
     * @param string $path
     *
     * @return bool
     */
    private static function rmDirRecursive($path)
    {
        $files = array_diff(scandir($path), ['.', '..']);
        foreach ($files as $file) {
            (is_dir("$path/$file")) ? self::rmDirRecursive("$path/$file") : unlink("$path/$file");
        }
        return rmdir($path);
    }
}
