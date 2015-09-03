<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Fixer\Symfony;

use SplFileInfo;
use Symfony\CS\AbstractFixer;
use Symfony\CS\Tokenizer\Token;
use Symfony\CS\Tokenizer\Tokens;
use Symfony\CS\Tokenizer\TokensAnalyzer;

/**
 * Make sure there is one blank line above and below a method.
 * The exception is when a method is the last item in a 'classy'.
 *
 * @author SpacePossum
 */
final class MethodSeparationFixer extends AbstractFixer
{
    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return 'Methods must be separated with one blank line.';
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        // Must run before braces and indentation fixers because this fixer
        // might add line breaks to the code without indenting.
        return 55;
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isAnyTokenKindsFound(Token::getClassyTokenKinds());
    }

    /**
     * {@inheritdoc}
     */
    public function fix(SplFileInfo $file, Tokens $tokens)
    {
        $tokensAnalyzer = new TokensAnalyzer($tokens);

        for ($index = $tokens->getSize() - 1; $index > 0;--$index) {
            if (!$tokens[$index]->isClassy()) {
                continue;
            }

            // figure out where the classy starts
            $classStart = $tokens->getNextTokenOfKind($index, array('{'));

            // figure out where the classy ends
            $classEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $classStart);

            if ($tokens[$index]->isGivenKind(T_INTERFACE)) {
                $this->fixInterface($tokens, $classStart, $classEnd);
            } else {
                // classes and traits can be fixed the same way
                $this->fixClass($tokens, $tokensAnalyzer, $classStart, $classEnd);
            }
        }
    }

    /**
     * @param Tokens         $tokens
     * @param TokensAnalyzer $tokensAnalyzer
     * @param int            $classStart
     * @param int            $classEnd
     */
    private function fixClass(Tokens $tokens, TokensAnalyzer $tokensAnalyzer, $classStart, $classEnd)
    {
        for ($index = $classEnd; $index > $classStart; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION) || $tokensAnalyzer->isLambda($index)) {
                continue;
            }

            $attributes = $tokensAnalyzer->getMethodAttributes($index);
            if (true === $attributes['abstract']) {
                $methodEnd = $tokens->getNextTokenOfKind($index, array(';'));
            } else {
                $methodStart = $tokens->getNextTokenOfKind($index, array('{'));
                $methodEnd = $tokens->findBlockEnd(Tokens::BLOCK_TYPE_CURLY_BRACE, $methodStart, true);
            }

            $this->fixSpaceBelowMethod($tokens, $classEnd, $methodEnd);
            $this->fixSpaceAboveMethod($tokens, $classStart, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classStart
     * @param int    $classEnd
     */
    private function fixInterface(Tokens $tokens, $classStart, $classEnd)
    {
        for ($index = $classEnd; $index > $classStart; --$index) {
            if (!$tokens[$index]->isGivenKind(T_FUNCTION)) {
                continue;
            }

            $methodEnd = $tokens->getNextTokenOfKind($index, array(';'));

            $this->fixSpaceBelowMethod($tokens, $classEnd, $methodEnd);
            $this->fixSpaceAboveMethod($tokens, $classStart, $index);
        }
    }

    /**
     * @param Tokens $tokens
     * @param int    $classEnd
     * @param int    $methodEnd
     */
    private function fixSpaceBelowMethod(Tokens $tokens, $classEnd, $methodEnd)
    {
        $nextNotWhite = $tokens->getNextNonWhitespace($methodEnd);
        $this->correctLineBreaks($tokens, $methodEnd, $nextNotWhite, $nextNotWhite === $classEnd ? 1 : 2);
    }

    /**
     * Fix spacing above a method signature. Deal with comments, PHPDocs and spaces above the method.
     *
     * @param Tokens $tokens
     * @param int    $classStart  index of the class Token the method is in
     * @param int    $methodIndex index of the method to fix
     */
    private function fixSpaceAboveMethod(Tokens $tokens, $classStart, $methodIndex)
    {
        static $methodAttr = array(T_PRIVATE, T_PROTECTED, T_PUBLIC, T_ABSTRACT, T_FINAL, T_STATIC);

        // find out where the method signature starts
        $firstMethodAttrIndex = $methodIndex;
        for ($i = $methodIndex; $i > $classStart; --$i) {
            $nonWhiteAbove = $tokens->getNonWhitespaceSibling($i, -1);
            if (null !== $nonWhiteAbove && $tokens[$nonWhiteAbove]->isGivenKind($methodAttr)) {
                $firstMethodAttrIndex = $nonWhiteAbove;
            } else {
                break;
            }
        }

        if (false === $tokens[$nonWhiteAbove]->isGivenKind(T_DOC_COMMENT)) {
            $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, $nonWhiteAbove === $classStart ? 1 : 2);

            return;
        }

        // there should be one linebreak between the method signature and the PHPDoc above it
        $this->correctLineBreaks($tokens, $nonWhiteAbove, $firstMethodAttrIndex, 1);

        // there should be one blank line between the PHPDoc and whatever is above
        $nonWhiteAbovePHPDoc = $tokens->getNonWhitespaceSibling($nonWhiteAbove, -1);
        $this->correctLineBreaks($tokens, $nonWhiteAbovePHPDoc, $nonWhiteAbove, $nonWhiteAbovePHPDoc === $classStart ? 1 : 2);
    }

    private function correctLineBreaks(Tokens $tokens, $startIndex, $endIndex, $reqLineCount = 2)
    {
        ++$startIndex;
        $numbOfWhiteTokens = $endIndex - $startIndex;
        if (0 === $numbOfWhiteTokens) {
            $tokens->insertAt($startIndex, new Token(array(T_WHITESPACE, str_repeat("\n", $reqLineCount))));

            return;
        }

        $lineBreakCount = $this->getLineBreakCount($tokens, $startIndex, $endIndex);
        if ($reqLineCount === $lineBreakCount) {
            return;
        }

        if ($lineBreakCount < $reqLineCount) {
            $tokens[$startIndex]->setContent(str_repeat("\n", $reqLineCount - $lineBreakCount).$tokens[$startIndex]->getContent());

            return;
        }

        // $lineCount = > $reqLineCount : check the one Token case first since this one will be true most of the time
        if (1 === $numbOfWhiteTokens) {
            $tokens[$startIndex]->setContent(preg_replace('/[\r\n]/', '', $tokens[$startIndex]->getContent(), $lineBreakCount - $reqLineCount));

            return;
        }

        // $numbOfWhiteTokens = > 1
        $toReplaceCount = $lineBreakCount - $reqLineCount;
        for ($i = $startIndex; $i < $endIndex && $toReplaceCount > 0; ++$i) {
            $tokenLineCount = substr_count($tokens[$i]->getContent(), "\n");
            if ($tokenLineCount > 0) {
                $tokens[$i]->setContent(preg_replace('/[\r\n]/', '', $tokens[$i]->getContent(), min($toReplaceCount, $tokenLineCount)));
                $toReplaceCount -= $tokenLineCount;
            }
        }
    }

    private function getLineBreakCount(Tokens $tokens, $whiteStart, $whiteEnd)
    {
        $lineCount = 0;
        for ($i = $whiteStart; $i < $whiteEnd; ++$i) {
            $lineCount += substr_count($tokens[$i]->getContent(), "\n");
        }

        return $lineCount;
    }
}
