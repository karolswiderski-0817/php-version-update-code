<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Contrib;

use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Tokens;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class StrictFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(array(T_IS_EQUAL, T_IS_NOT_EQUAL));
    }

    /**
     * {@inheritdoc}
     */
    public function isRisky()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, Tokens $tokens)
    {
        static $map = array(
            T_IS_EQUAL => array(
                'id' => T_IS_IDENTICAL,
                'content' => '===',
            ),
            T_IS_NOT_EQUAL => array(
                'id' => T_IS_NOT_IDENTICAL,
                'content' => '!==',
            ),
        );

        foreach ($tokens as $index => $token) {
            $tokenId = $token->getId();

            if (isset($map[$tokenId])) {
                $tokens->overrideAt($index, array($map[$tokenId]['id'], $map[$tokenId]['content']));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Comparison should be strict.';
    }
}
