<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Linter;

use PhpCsFixer\Linter\ProcessLinter;
use PhpCsFixer\Test\AccessibleObject;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Linter\ProcessLinter
 * @covers \PhpCsFixer\Linter\ProcessLintingResult
 */
final class ProcessLinterTest extends AbstractLinterTestCase
{
    public function testIsAsync()
    {
        $this->assertTrue($this->createLinter()->isAsync());
    }

    /**
     * @param string $executable
     * @param string $file
     * @param string $expected
     *
     * @testWith ["php", "foo.php", "'php' '-l' 'foo.php'"]
     *           ["C:\\Program Files\\php\\php.exe", "foo bar\\baz.php", "'C:\\Program Files\\php\\php.exe' '-l' 'foo bar\\baz.php'"]
     * @requires OS Linux
     */
    public function testPrepareCommandOnPhpOnLinux($executable, $file, $expected)
    {
        $this->assertSame(
            $expected,
            AccessibleObject::create(new ProcessLinter($executable))->prepareProcess($file)->getCommandLine()
        );
    }

    /**
     * @param string $executable
     * @param string $file
     * @param string $expected
     *
     * @testWith ["php", "foo.php", "php -l foo.php"]
     *           ["C:\\Program Files\\php\\php.exe", "foo bar\\baz.php", "\"C:\\Program Files\\php\\php.exe\" -l \"foo bar\\baz.php\""]
     * @requires OS Win
     */
    public function testPrepareCommandOnPhpOnWindows($executable, $file, $expected)
    {
        $this->assertSame(
            $expected,
            AccessibleObject::create(new ProcessLinter($executable))->prepareProcess($file)->getCommandLine()
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function createLinter()
    {
        return new ProcessLinter();
    }
}
