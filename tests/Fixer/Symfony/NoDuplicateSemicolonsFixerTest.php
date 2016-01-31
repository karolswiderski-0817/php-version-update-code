<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class NoDuplicateSemicolonsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php $foo = 2 ; //

                ',
                '<?php $foo = 2 ; //
                    ;

                ',
            ),
            array(
                '<?php $foo = 3; /**/',
                '<?php $foo = 3; /**/; ;',
            ),
            array(
                '<?php $foo = 1;',
                '<?php $foo = 1;;;',
            ),
            array(
                '<?php $foo = 4;',
                '<?php $foo = 4;; ;;',
            ),
            array(
                '<?php $foo = 5;',
                '<?php $foo = 5;;
;
    ;',
            ),
            array(
                '<?php $foo = 6; ',
                '<?php $foo = 6;; ',
            ),
            array(
                '<?php for ($i = 7; ; ++$i) {}',
            ),
            array(
                '<?php
                    switch($a){
                        case 8;
                            echo 2;
                    }
                ',
                '<?php
                    switch($a){
                        case 8;;
                            echo 2;
                    }
                ',
            ),
        );
    }
}
