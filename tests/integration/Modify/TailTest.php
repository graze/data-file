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

use Graze\DataFile\Helper\Process\ProcessFactory;
use Graze\DataFile\Modify\FileModifierInterface;
use Graze\DataFile\Modify\Tail;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\AbstractFileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class TailTest extends AbstractFileTestCase
{
    /**
     * @var Tail
     */
    protected $tail;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->tail = new Tail();
        $this->tail->setProcessFactory($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FileModifierInterface::class, $this->tail);
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock(LocalFile::class);
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->tail->canModify($localFile));
        static::assertFalse(
            $this->tail->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock(FileNodeInterface::class);

        static::assertFalse($this->tail->canModify($randomThing));
    }

    public function testBasicReadingTheLastNLines()
    {
        $file = $this->createFile('last_five_lines');

        $newFile = $this->tail->tail($file, 5);

        static::assertEquals(
            [
                "Line 6",
                "Line 7",
                "Line 8",
                "Line 9",
                "Line 10",
            ],
            $newFile->getContents()
        );

        $newFile = $this->tail->tail($file, 2);

        static::assertEquals(
            [
                "Line 9",
                "Line 10",
            ],
            $newFile->getContents()
        );
    }

    /**
     * @param string $path
     *
     * @return LocalFile
     */
    private function createFile($path)
    {
        $file = new LocalFile(static::$dir . $path);
        $file->put(
            "Line 1
Line 2
Line 3
Line 4
Line 5
Line 6
Line 7
Line 8
Line 9
Line 10
"
        );

        return $file;
    }

    public function testOutputLinesStartingFromN()
    {
        $file = $this->createFile('from.second.line.onwards');

        $newFile = $this->tail->tail($file, '+2');

        static::assertEquals(
            [
                "Line 2",
                "Line 3",
                "Line 4",
                "Line 5",
                "Line 6",
                "Line 7",
                "Line 8",
                "Line 9",
                "Line 10",
            ],
            $newFile->getContents()
        );
    }

    public function testAddingAPostfixToTheEndOfTheFile()
    {
        $file = $this->createFile('postfix_test.test');

        $newFile = $this->tail->tail($file, 4, ['postfix' => 'pfixtest']);

        static::assertNotNull($newFile);
        static::assertEquals('postfix_test-pfixtest.test', $newFile->getFilename());
    }

    public function testCallingWithBlankPostfixWillReplaceInLine()
    {
        $file = $this->createFile('inline_tail.test');

        $newFile = $this->tail->tail($file, 2, ['postfix' => '']);

        static::assertNotNull($newFile);
        static::assertEquals($file->getFilename(), $newFile->getFilename());
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = $this->createFile('inline_replace.test');

        $newFile = $this->tail->tail($file, 5, ['keepOldFile' => false]);

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
    }

    public function testCallingModifyDoesTail()
    {
        $file = $this->createFile('simple_tail.test');

        $newFile = $this->tail->modify($file, ['lines' => 4]);

        static::assertEquals(
            [
                "Line 7",
                "Line 8",
                "Line 9",
                "Line 10",
            ],
            $newFile->getContents()
        );
    }

    public function testCallingModifyWillPassThroughOptions()
    {
        $file = $this->createFile('option_pass_through.test');

        $newFile = $this->tail->modify(
            $file,
            [
                'lines'       => 2,
                'postfix'     => 'pass',
                'keepOldFile' => false,
            ]
        );

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
        static::assertNotNull($newFile);
        static::assertEquals('option_pass_through-pass.test', $newFile->getFilename());
    }

    public function testCallingModifyWithoutLinesWillThrowAnException()
    {
        $file = $this->createFile('option_pass_through.test');

        $this->expectException(InvalidArgumentException::class);

        $this->tail->modify($file);
    }

    public function testCallingModifyWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);
        $file->shouldReceive('__toString')
             ->andReturn('some/file/here');

        $this->expectException(InvalidArgumentException::class);

        $this->tail->modify($file, ['lines' => 1]);
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_tail.test');
        $file->put('nothing interesting here');

        $this->expectException(ProcessFailedException::class);

        $this->tail->tail($file, 3);
    }
}
