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

namespace PhpCsFixer\Tests\Fixer\PhpTag;

use PhpCsFixer\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Ceeram <ceeram@cakephp.org>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class BlankLineAfterOpeningTagFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @requires PHP 5.4
     * @dataProvider provideCases54
     */
    public function testFix54($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php

$a = function(){
                    echo 1;
                };',
                '<?php $a = function(){
                    echo 1;
                };',
            ),
            array(
                '<?php

 class SomeClass
 {
     const VERSION = "1.1.1";
     const FOO = "bar";
 }
',
            ),
            array(
                '<?php $foo = true; ?>',
            ),
            array(
                '<?php $foo = true; ?>
',
            ),
            array(
                '<?php

$foo = true;
?>',
                '<?php
$foo = true;
?>',
            ),
            array(
                '<?php

$foo = true;
$bar = false;
',
                '<?php $foo = true;
$bar = false;
',
            ),
            array(
                '<?php

$foo = true;
?>
Html here
<?php $bar = false;',
            ),
            array(
                '<?php
$foo = true;
?>
Html here
<?php $bar = false;
',
            ),
        );
    }

    public function provideCases54()
    {
        return array(
            array(
                '<?= $bar;
$foo = $bar;
?>',
            ),
        );
    }

    /**
     * @dataProvider provideMessyWhitespacesCases
     */
    public function testMessyWhitespaces($expected, $input = null)
    {
        $fixer = clone $this->getFixer();
        $fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input, null, $fixer);
    }

    public function provideMessyWhitespacesCases()
    {
        return array(
            array(
                "<?php\r\n\r\n\$foo = true;\r\n",
                "<?php \$foo = true;\r\n",
            ),
            array(
                "<?php\r\n\r\n\$foo = true;\r\n",
                "<?php\r\n\$foo = true;\r\n",
            ),
        );
    }
}
