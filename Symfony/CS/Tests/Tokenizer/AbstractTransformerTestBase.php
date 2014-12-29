<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Tokenizer;

use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
abstract class AbstractTransformerTestBase extends \PHPUnit_Framework_TestCase
{
    protected function makeTest($source, array $expectedTokens = array())
    {
        $tokens = Tokens::fromCode($source);

        $this->assertSame(
            count($expectedTokens),
            array_sum(array_map(
                function ($item) { return count($item); },
                $tokens->findGivenKind(array_map(function ($name) { return constant($name); }, $expectedTokens))
            ))
        );

        foreach ($expectedTokens as $index => $name) {
            $this->assertSame($name, $tokens[$index]->getName());
            $this->assertSame(constant($name), $tokens[$index]->getId());
        }
    }
}
