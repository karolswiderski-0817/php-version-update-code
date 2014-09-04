<?php

/*
 * This file is part of the Symfony CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\All;

use Symfony\CS\FixerInterface;
use Symfony\CS\Tokens;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SingleArrayNoTrailingCommaFixer implements FixerInterface
{
    /**
     * {@inheritdoc}
     */
    public function fix(\SplFileInfo $file, $content)
    {
        $tokens = Tokens::fromCode($content);

        for ($index = 0, $c = $tokens->count(); $index < $c; ++$index) {
            if ($tokens->isArray($index)) {
                $this->fixArray($tokens, $index);
            }
        }

        return $tokens->generateCode();
    }

    /**
     * {@inheritdoc}
     */
    public function getLevel()
    {
        return FixerInterface::ALL_LEVEL;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(\SplFileInfo $file)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'single_array_no_trailing_comma';
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'PHP single-line arrays should not have trailing comma.';
    }

    private function fixArray(Tokens $tokens, &$index)
    {
        $bracesLevel = 0;

        // Skip only when its an array, for short arrays we need the brace for correct
        // level counting
        if ($tokens[$index]->isGivenKind(T_ARRAY)) {
            ++$index;
        }

        $multiline = $tokens->isArrayMultiLine($index);

        for ($c = $tokens->count(); $index < $c; ++$index) {
            $token = $tokens[$index];

            if ('(' === $token->content || '[' === $token->content) {
                ++$bracesLevel;

                continue;
            }

            if ($token->isGivenKind(T_ARRAY) || $tokens->isShortArray($index)) {
                $this->fixArray($tokens, $index);

                continue;
            }

            if (')' === $token->content || ']' === $token->content) {
                --$bracesLevel;

                $foundIndex = null;

                if (!$multiline && 0 === $bracesLevel && ',' === $tokens->getPrevNonWhitespace($index, array(), $foundIndex)->content) {
                    $tokens->removeTrailingWhitespace($foundIndex);
                    $tokens[$foundIndex]->clear();
                }

                if (0 === $bracesLevel) {
                    break;
                }
            }
        }
    }
}
