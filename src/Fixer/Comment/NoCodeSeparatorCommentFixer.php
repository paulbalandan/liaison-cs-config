<?php

/**
 * This file is part of Liaison CS Config Factory.
 *
 * (c) 2020 John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Liaison\CS\Config\Fixer\Comment;

use Liaison\CS\Config\Fixer\AbstractCustomFixer;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Tokens;
use SplFileInfo;

/**
 * Removes superfluous code separators.
 */
final class NoCodeSeparatorCommentFixer extends AbstractCustomFixer
{
    /**
     * {@inheritdoc}
     *
     * Must run before NoExtraBlankLinesFixer, NoTrailingWhitespaceFixer, NoWhitespaceInBlankLineFixer.
     * Must run after PhpdocToCommentFixer.
     */
    public function getPriority()
    {
        return 2;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinition()
    {
        return new FixerDefinition(
            'There should not be any code separator comments.',
            [new CodeSample("<?php\n//--------------\n// --------\n")]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function isCandidate(Tokens $tokens)
    {
        return $tokens->isTokenKindFound(T_COMMENT);
    }

    /**
     * {@inheritdoc}
     */
    protected function applyFix(SplFileInfo $file, Tokens $tokens): void
    {
        /** @var \PhpCsFixer\Tokenizer\Token $token */
        foreach ($tokens as $index => $token) {
            if (!$token->isGivenKind(T_COMMENT)) {
                continue;
            }

            if (!$this->isCodeSeparator($token->getContent())) {
                continue;
            }

            if ($this->isCommentBlockBoundary($tokens, $index)) {
                continue;
            }

            $tokens->removeLeadingWhitespace($index);
            $tokens->clearTokenAndMergeSurroundingWhitespace($index);
        }
    }

    private function isCommentBlockBoundary(Tokens $tokens, int $index): bool
    {
        $prevIndex = $tokens->getPrevNonWhitespace($index);
        $nextIndex = $tokens->getNextNonWhitespace($index);

        if (null === $prevIndex) {
            $prevTokenIsCodeSeparator = false; // @codeCoverageIgnore
        } else {
            /** @var \PhpCsFixer\Tokenizer\Token $prevToken */
            $prevToken = $tokens[$prevIndex];

            $prevTokenIsCodeSeparator = $prevToken->isGivenKind(T_COMMENT) && !$this->isCodeSeparator($prevToken->getContent());
        }

        if (null === $nextIndex) {
            $nextTokenIsCodeSeparator = false;
        } else {
            /** @var \PhpCsFixer\Tokenizer\Token $nextToken */
            $nextToken = $tokens[$nextIndex];

            $nextTokenIsCodeSeparator = $nextToken->isGivenKind(T_COMMENT) && !$this->isCodeSeparator($nextToken->getContent());
        }

        return $prevTokenIsCodeSeparator || $nextTokenIsCodeSeparator;
    }

    private function isCodeSeparator(string $content): bool
    {
        return Preg::match('|^//[\s\-]*$|', $content) === 1;
    }
}
