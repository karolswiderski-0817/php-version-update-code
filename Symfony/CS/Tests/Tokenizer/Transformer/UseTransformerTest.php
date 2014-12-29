<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer\Transformer;

use Symfony\CS\Tests\Tokenizer\AbstractTransformerTestBase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class UseTransformerTest extends AbstractTransformerTestBase
{
    /**
     * @dataProvider provideProcessCases
     */
    public function testProcess($source, array $expectedTokens = array())
    {
        $this->makeTest($source, $expectedTokens);
    }

    public function provideProcessCases()
    {
        return array(
            array(
                '<?php use Foo;',
                array(
                    1 => 'T_USE',
                ),
            ),
            array(
                '<?php function foo() use ($bar) {}',
                array(
                    7 => 'CT_USE_LAMBDA',
                ),
            ),
            array(
                '<?php class Foo { use Bar; }',
                array(
                    7 => 'CT_USE_TRAIT',
                ),
            ),
            array(
                '<?php namespace Aaa; use Bbb; class Foo { use Bar; function baz() { $a=1; return function () use ($a) {}; } }',
                array(
                    6 => 'T_USE',
                    17 => 'CT_USE_TRAIT',
                    42 => 'CT_USE_LAMBDA',
                ),
            ),
        );
    }
}
