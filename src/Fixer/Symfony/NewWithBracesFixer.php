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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NewWithBracesFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_NEW);
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        for ($index = $tokens->count() - 3; $index > 0; --$index) {
            $token = $tokens[$index];

            if (!$token->isGivenKind(T_NEW)) {
                continue;
            }

            $nextIndex = $tokens->getNextTokenOfKind(
                $index,
                array(':', ';', ',', '(', ')', '[', ']', array(T_CLOSE_TAG), array(CT_ARRAY_SQUARE_BRACE_OPEN), array(CT_ARRAY_SQUARE_BRACE_CLOSE), array(CT_BRACE_CLASS_INSTANTIATION_OPEN), array(CT_BRACE_CLASS_INSTANTIATION_CLOSE))
            );
            $nextToken = $tokens[$nextIndex];

            // entrance into array index syntax - need to look for exit
            while ($nextToken->equals('[')) {
                $nextIndex = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_INDEX_SQUARE_BRACE, $nextIndex) + 1;
                $nextToken = $tokens[$nextIndex];
            }

            // new statement has a gap in it - advance to the next token
            if ($nextToken->isWhitespace()) {
                $nextIndex = $tokens->getNextNonWhitespace($nextIndex);
                $nextToken = $tokens[$nextIndex];
            }

            // new statement with () - nothing to do
            if ($nextToken->equals('(')) {
                continue;
            }

            $meaningBeforeNextIndex = $tokens->getPrevMeaningfulToken($nextIndex);

            $tokens->insertAt($meaningBeforeNextIndex + 1, array(new Token('('), new Token(')')));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'All instances created with new keyword must be followed by braces.';
    }
}
