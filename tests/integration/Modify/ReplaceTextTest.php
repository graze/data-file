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
use Graze\DataFile\Modify\ReplaceText;
use Graze\DataFile\Node\FileNodeInterface;
use Graze\DataFile\Node\LocalFile;
use Graze\DataFile\Test\FileTestCase;
use InvalidArgumentException;
use Mockery as m;
use Mockery\MockInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ReplaceTextTest extends FileTestCase
{
    /**
     * @var ReplaceText
     */
    protected $replacer;

    /**
     * @var ProcessFactory|MockInterface
     */
    protected $processFactory;

    public function setUp()
    {
        $this->processFactory = m::mock(ProcessFactory::class)->makePartial();
        $this->replacer = new ReplaceText();
        $this->replacer->setProcessFactory($this->processFactory);
    }

    public function testInstanceOf()
    {
        static::assertInstanceOf(FileModifierInterface::class, $this->replacer);
    }

    public function testCanModifyAcceptsLocalFile()
    {
        $localFile = m::mock(LocalFile::class);
        $localFile->shouldReceive('exists')->andReturn(true, false);

        static::assertTrue($this->replacer->canModify($localFile));
        static::assertFalse(
            $this->replacer->canModify($localFile),
            "CanExtend should return false if the file does not exist"
        );

        $randomThing = m::mock(FileNodeInterface::class);

        static::assertFalse($this->replacer->canModify($randomThing));
    }

    public function testReplaceTextReplacesASingleEntry()
    {
        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants');

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntries()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text', 'some'], ['pants', 'many']);

        static::assertNotNull($newFile);
        static::assertEquals(['many pants that pants should be replaced'], $newFile->getContents());
    }

    public function testReplaceTextReplacesMultipleEntriesWorksInCompound()
    {
        $file = new LocalFile(static::$dir . 'multiple_compound_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, ['text', 'pants that'], ['pants', 'fish like']);

        static::assertNotNull($newFile);
        static::assertEquals(['some fish like pants should be replaced'], $newFile->getContents());
    }

    public function testCallingReplaceTextWithArraysThatHaveMismatchedCountsThrowsAnException()
    {
        $file = new LocalFile(static::$dir . 'multiple_replace_failure.test');
        $file->put('some text that text should be replaced');

        $this->expectException(InvalidArgumentException::class);

        $this->replacer->replaceText($file, ['text', 'pants that'], ['pants']);
    }

    public function testAddingAPostfixToTheEndOfTheFile()
    {
        $file = new LocalFile(static::$dir . 'postfix_test.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => 'pfixtest']);

        static::assertNotNull($newFile);
        static::assertEquals('postfix_test-pfixtest.test', $newFile->getFilename());
    }

    public function testCallingWithBlankPostfixWillReplaceInLine()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['postfix' => '']);

        static::assertNotNull($newFile);
        static::assertEquals($file->getFilename(), $newFile->getFilename());
    }

    public function testSettingKeepOldFileToFalseWillDeleteTheOldFile()
    {
        $file = new LocalFile(static::$dir . 'inline_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants', ['keepOldFile' => false]);

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
    }

    public function testCallingModifyReplacesText()
    {
        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->modify($file, ['fromText' => 'text', 'toText' => 'pants']);

        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testCallingModifyWillPassThroughOptions()
    {
        $file = new LocalFile(static::$dir . 'option_pass_through.test');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->modify(
            $file,
            [
                'fromText'    => 'text',
                'toText'      => 'pants',
                'postfix'     => 'pass',
                'keepOldFile' => false,
            ]
        );

        static::assertTrue($newFile->exists());
        static::assertFalse($file->exists());
        static::assertNotNull($newFile);
        static::assertEquals('option_pass_through-pass.test', $newFile->getFilename());
    }

    public function testCallingModifyWithNoFromTextThrowsInvalidArgumentsException()
    {
        $this->expectException(InvalidArgumentException::class);

        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $this->replacer->modify($file, ['toText' => 'pants']);
    }

    public function testCallingModifyWithNoToTextThrowsInvalidArgumentsException()
    {
        $this->expectException(InvalidArgumentException::class);

        $file = new LocalFile(static::$dir . 'simple_replace.test');
        $file->put('some text that text should be replaced');

        $this->replacer->modify($file, ['fromText' => 'pants']);
    }

    public function testCallingModifyWithANonLocalFileWillThrowAnException()
    {
        $file = m::mock(FileNodeInterface::class);
        $file->shouldReceive('__toString')
             ->andReturn('some/file/here');

        $this->expectException(InvalidArgumentException::class);

        $this->replacer->modify($file, ['fromText' => 'pants', 'toText' => 'more pants']);
    }

    public function testCallingReplaceTextOnAFileWithoutAnExtensionWorks()
    {
        $file = new LocalFile(static::$dir . 'file_no_ext');
        $file->put('some text that text should be replaced');

        $newFile = $this->replacer->replaceText($file, 'text', 'pants');

        static::assertTrue($newFile->exists());
        static::assertNotNull($newFile);
        static::assertEquals(['some pants that pants should be replaced'], $newFile->getContents());
    }

    public function testWhenTheProcessFailsAnExceptionIsThrownOnFindEncoding()
    {
        $process = m::mock(Process::class)->makePartial();
        $process->shouldReceive('isSuccessful')->andReturn(false);
        $this->processFactory->shouldReceive('createProcess')
                             ->andReturn($process);

        $file = new LocalFile(static::$dir . 'failed_replace_text.test');
        $file->put('some text that text should be replaced');

        $this->expectException(ProcessFailedException::class);

        $this->replacer->replaceText($file, 'text', 'pants');
    }
}
