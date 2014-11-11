<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Carlos Cirello <carlos.cirello.nl@gmail.com>
 */
class AlignDoubleArrowFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideFixCases()
    {
        return array(
            array(
                '<?php
                    $a = [
                        0  => 1,
                        10 => [
                            1  => 2,
                            22 => 3,
                        ],
                        100 => [
                            1  => 2,
                            22 => 3,
                        ]
                    ];
                ',
            ),
            array(
                '<?php
                    $a = array(
                        0  => 1,
                        10 => array(
                            1  => 2,
                            22 => 3,
                        ),
                        100 => array(
                            1  => 2,
                            22 => 3,
                        )
                    );
                ',
            ),
            array(
                '<?php
                $arr = array(
                $a    => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                );
                ',
                '<?php
                $arr = array(
                $a => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                );
                ',
            ),
            array(
                '<?php
                $arr = [
                $a    => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                ];
                ',
                '<?php
                $arr = [
                $a => 1,
                $bbbb => \'
                $cccccccc = 3;
                \',
                ];
                ',
            ),
            array(
                '<?php
                foreach($arr as $k => $v){
                    $arr = array($k => 1,
                        $a          => 1,
                        $bbbb       => \'
                        $cccccccc = 3;
                        \',
                    );
                }
                ',
            ),
        );
    }
}
