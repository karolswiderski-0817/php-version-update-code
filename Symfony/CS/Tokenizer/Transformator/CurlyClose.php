<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tokenizer\Transformator;

use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\AbstractTransformator;

/**
 * Transform closing `}` for T_CURLY_OPEN into CT_CURLY_CLOSE.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class CurlyClose extends AbstractTransformator
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_CURLY_OPEN) as $index => $token) {
            $level = 1;
            $nestIndex = $index;

            while (0 < $level) {
                ++$nestIndex;

                // we count all kind of {
                if ('{' === $tokens[$nestIndex]->content) {
                    ++$level;
                    continue;
                }

                // we count all kind of }
                if ('}' === $tokens[$nestIndex]->content) {
                    --$level;
                }
            }

            $tokens[$nestIndex] = new Token(array(CT_CURLY_CLOSE, '}', $tokens[$nestIndex]->line));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_CURLY_CLOSE');
    }
}
