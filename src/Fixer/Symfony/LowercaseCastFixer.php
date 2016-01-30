<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Fixer\Symfony;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;

/**
 * @author SpacePossum
 */
final class LowercaseCastFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getCastTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = 0, $count = $tokens->count(); $index  < $count; ++$index) {
            if (!$tokens[$index]->isCast()) {
                continue;
            }

            $tokens[$index]->setContent(strtolower($tokens[$index]->getContent()));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Cast should be written in lower case.';
    }
}
