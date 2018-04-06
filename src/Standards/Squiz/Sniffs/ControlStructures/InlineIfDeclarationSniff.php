<?php
/**
 * Tests the spacing of shorthand IF statements.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class InlineIfDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_INLINE_THEN];

    }//end register()


    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        $openBracket  = null;
        $closeBracket = null;
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            $parens       = $tokens[$stackPtr]['nested_parenthesis'];
            $openBracket  = array_pop($parens);
            $closeBracket = $tokens[$openBracket]['parenthesis_closer'];
        }

        // Find the beginning of the statement. If we don't find a
        // semicolon (end of statement) or comma (end of array value)
        // then assume the content before the closing parenthesis is the end.
        $else         = $phpcsFile->findNext(T_INLINE_ELSE, ($stackPtr + 1));
        $statementEnd = $phpcsFile->findNext([T_SEMICOLON, T_COMMA], ($else + 1), $closeBracket);
        if ($statementEnd === false) {
            $statementEnd = $phpcsFile->findPrevious(T_WHITESPACE, ($closeBracket - 1), null, true);
        }

        // Make sure it's all on the same line.
        if ($tokens[$statementEnd]['line'] !== $tokens[$stackPtr]['line']) {
            $error = 'Inline shorthand IF statement must be declared on a single line';
            $phpcsFile->addError($error, $stackPtr, 'NotSingleLine');
            return;
        }

        // Make sure there are spaces around the question mark.
        $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        $contentAfter  = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$contentBefore]['code'] !== T_CLOSE_PARENTHESIS) {
            $error = 'Inline shorthand IF statement requires brackets around comparison';
            $phpcsFile->addError($error, $stackPtr, 'NoBrackets');
        }

        $spaceBefore = ($tokens[$stackPtr]['column'] - ($tokens[$contentBefore]['column'] + $tokens[$contentBefore]['length']));
        if ($spaceBefore !== 1) {
            $error = 'Inline shorthand IF statement requires 1 space before THEN; %s found';
            $data  = [$spaceBefore];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingBeforeThen', $data);
            if ($fix === true) {
                if ($spaceBefore === 0) {
                    $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
                }
            }
        }

        // If there is no content between the ? and the : operators, then they are
        // trying to replicate an elvis operator, even though PHP doesn't have one.
        // In this case, we want no spaces between the two operators so ?: looks like
        // an operator itself.
        $next = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        if ($tokens[$next]['code'] === T_INLINE_ELSE) {
            $inlineElse = $next;
            if ($inlineElse !== ($stackPtr + 1)) {
                $error = 'Inline shorthand IF statement without THEN statement requires 0 spaces between THEN and ELSE';
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'ElvisSpacing');
                if ($fix === true) {
                    $phpcsFile->fixer->replaceToken(($stackPtr + 1), '');
                }
            }
        } else {
            $spaceAfter = (($tokens[$contentAfter]['column']) - ($tokens[$stackPtr]['column'] + 1));
            if ($spaceAfter !== 1) {
                $error = 'Inline shorthand IF statement requires 1 space after THEN; %s found';
                $data  = [$spaceAfter];
                $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpacingAfterThen', $data);
                if ($fix === true) {
                    if ($spaceAfter === 0) {
                        $phpcsFile->fixer->addContent($stackPtr, ' ');
                    } else {
                        $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
                    }
                }
            }

            // Make sure the ELSE has the correct spacing.
            $inlineElse    = $phpcsFile->findNext(T_INLINE_ELSE, ($stackPtr + 1), $statementEnd, false);
            $contentBefore = $phpcsFile->findPrevious(T_WHITESPACE, ($inlineElse - 1), null, true);
            $spaceBefore   = ($tokens[$inlineElse]['column'] - ($tokens[$contentBefore]['column'] + $tokens[$contentBefore]['length']));
            if ($spaceBefore !== 1) {
                $error = 'Inline shorthand IF statement requires 1 space before ELSE; %s found';
                $data  = [$spaceBefore];
                $fix   = $phpcsFile->addFixableError($error, $inlineElse, 'SpacingBeforeElse', $data);
                if ($fix === true) {
                    if ($spaceBefore === 0) {
                        $phpcsFile->fixer->addContentBefore($inlineElse, ' ');
                    } else {
                        $phpcsFile->fixer->replaceToken(($inlineElse - 1), ' ');
                    }
                }
            }
        }//end if

        $contentAfter = $phpcsFile->findNext(T_WHITESPACE, ($inlineElse + 1), null, true);
        $spaceAfter   = (($tokens[$contentAfter]['column']) - ($tokens[$inlineElse]['column'] + 1));
        if ($spaceAfter !== 1) {
            $error = 'Inline shorthand IF statement requires 1 space after ELSE; %s found';
            $data  = [$spaceAfter];
            $fix   = $phpcsFile->addFixableError($error, $inlineElse, 'SpacingAfterElse', $data);
            if ($fix === true) {
                if ($spaceAfter === 0) {
                    $phpcsFile->fixer->addContent($inlineElse, ' ');
                } else {
                    $phpcsFile->fixer->replaceToken(($inlineElse + 1), ' ');
                }
            }
        }

    }//end process()


}//end class
