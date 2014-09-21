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
 * Transform `array` typehint from T_ARRAY into T_ARRAY_TYPEHINT.
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class ArrayTypehint extends AbstractTransformator
{
    /**
     * {@inheritdoc}
     */
    public function process(Tokens $tokens)
    {
        foreach ($tokens->findGivenKind(T_ARRAY) as $index => $token) {
            $nextIndex = $tokens->getTokenNotOfKindSibling($index, 1, array(array(T_WHITESPACE), array(T_COMMENT), array(T_DOC_COMMENT)));
            $nextToken = $tokens[$nextIndex];

            if (!$nextToken->equals('(')) {
                $token->id = CT_ARRAY_TYPEHINT;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomTokenNames()
    {
        return array('CT_ARRAY_TYPEHINT');
    }
}
