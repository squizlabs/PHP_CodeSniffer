<?php
/**
 * Ensures USE blocks are declared correctly.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR2\Sniffs\Namespaces;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class UseDeclarationSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_USE];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        if ($this->shouldIgnoreUse($phpcsFile, $stackPtr) === true) {
            return;
        }

        $tokens = $phpcsFile->getTokens();

        // One space after the use keyword.
        if ($tokens[($stackPtr + 1)]['content'] !== ' ') {
            $error = 'There must be a single space after the USE keyword';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterUse');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($stackPtr + 1), ' ');
            }
        }

        // Only one USE declaration allowed per statement.
        $next = $phpcsFile->findNext([T_COMMA, T_SEMICOLON, T_OPEN_USE_GROUP, T_CLOSE_TAG], ($stackPtr + 1));
        if ($next !== false
            && $tokens[$next]['code'] !== T_SEMICOLON
            && $tokens[$next]['code'] !== T_CLOSE_TAG
        ) {
            $error = 'There must be one USE keyword per declaration';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'MultipleDeclarations');
            if ($fix === true) {
                if ($tokens[$next]['code'] === T_COMMA) {
                    $phpcsFile->fixer->replaceToken($next, ';'.$phpcsFile->eolChar.'use ');
                } else {
                    $baseUse      = rtrim($phpcsFile->getTokensAsString($stackPtr, ($next - $stackPtr)));
                    $closingCurly = $phpcsFile->findNext(T_CLOSE_USE_GROUP, ($next + 1));

                    $phpcsFile->fixer->beginChangeset();

                    // Remove base use statement.
                    for ($i = $stackPtr; $i <= $next; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    // Convert grouped use statements into full use statements.
                    do {
                        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), $closingCurly, true);

                        $whitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($next - 1), null, true);
                        for ($i = ($whitespace + 1); $i < $next; $i++) {
                            $phpcsFile->fixer->replaceToken($i, '');
                        }

                        if ($tokens[$next]['code'] === T_CONST || $tokens[$next]['code'] === T_FUNCTION) {
                            $phpcsFile->fixer->addContentBefore($next, 'use ');
                            $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($next + 1), $closingCurly, true);
                            $phpcsFile->fixer->addContentBefore($next, str_replace('use ', '', $baseUse));
                        } else {
                            $phpcsFile->fixer->addContentBefore($next, $baseUse);
                        }

                        $next = $phpcsFile->findNext(T_COMMA, ($next + 1), $closingCurly);
                        if ($next !== false) {
                            $phpcsFile->fixer->replaceToken($next, ';'.$phpcsFile->eolChar);
                        }
                    } while ($next !== false);

                    $phpcsFile->fixer->replaceToken($closingCurly, '');

                    // Remove any trailing whitespace.
                    $next       = $phpcsFile->findNext(T_SEMICOLON, $closingCurly);
                    $whitespace = $phpcsFile->findPrevious(T_WHITESPACE, ($closingCurly - 1), null, true);
                    for ($i = ($whitespace + 1); $i < $next; $i++) {
                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->endChangeset();
                }//end if
            }//end if
        }//end if

        // Make sure this USE comes after the first namespace declaration.
        $prev = $phpcsFile->findPrevious(T_NAMESPACE, ($stackPtr - 1));
        if ($prev !== false) {
            $first = $phpcsFile->findNext(T_NAMESPACE, 1);
            if ($prev !== $first) {
                $error = 'USE declarations must go after the first namespace declaration';
                $phpcsFile->addError($error, $stackPtr, 'UseAfterNamespace');
            }
        }

        // Only interested in the last USE statement from here onwards.
        $nextUse = $phpcsFile->findNext(T_USE, ($stackPtr + 1));
        while ($this->shouldIgnoreUse($phpcsFile, $nextUse) === true) {
            $nextUse = $phpcsFile->findNext(T_USE, ($nextUse + 1));
            if ($nextUse === false) {
                break;
            }
        }

        if ($nextUse !== false) {
            return;
        }

        $end = $phpcsFile->findNext(T_SEMICOLON, ($stackPtr + 1));
        if ($end === false) {
            return;
        }

        // Find either the start of the next line or the beginning of the next statement,
        // whichever comes first.
        for ($end = ++$end; $end < $phpcsFile->numTokens; $end++) {
            if (isset(Tokens::$emptyTokens[$tokens[$end]['code']]) === false) {
                break;
            }

            if ($tokens[$end]['column'] === 1) {
                // Reached the next line.
                break;
            }
        }

        --$end;

        if (($tokens[$end]['code'] === T_COMMENT
            || isset(Tokens::$phpcsCommentTokens[$tokens[$end]['code']]) === true)
            && substr($tokens[$end]['content'], 0, 2) === '/*'
            && substr($tokens[$end]['content'], -2) !== '*/'
        ) {
            // Multi-line block comments are not allowed as trailing comment after a use statement.
            --$end;
        }

        $next = $phpcsFile->findNext(T_WHITESPACE, ($end + 1), null, true);

        if ($next === false || $tokens[$next]['code'] === T_CLOSE_TAG) {
            return;
        }

        $diff = ($tokens[$next]['line'] - $tokens[$end]['line'] - 1);
        if ($diff !== 1) {
            if ($diff < 0) {
                $diff = 0;
            }

            $error = 'There must be one blank line after the last USE statement; %s found;';
            $data  = [$diff];
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'SpaceAfterLastUse', $data);
            if ($fix === true) {
                if ($diff === 0) {
                    $phpcsFile->fixer->addNewline($end);
                } else {
                    $phpcsFile->fixer->beginChangeset();
                    for ($i = ($end + 1); $i < $next; $i++) {
                        if ($tokens[$i]['line'] === $tokens[$next]['line']) {
                            break;
                        }

                        $phpcsFile->fixer->replaceToken($i, '');
                    }

                    $phpcsFile->fixer->addNewline($end);
                    $phpcsFile->fixer->endChangeset();
                }
            }
        }//end if

    }//end process()


    /**
     * Check if this use statement is part of the namespace block.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in
     *                                               the stack passed in $tokens.
     *
     * @return void
     */
    private function shouldIgnoreUse($phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        // Ignore USE keywords inside closures and during live coding.
        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] === T_OPEN_PARENTHESIS) {
            return true;
        }

        // Ignore USE keywords for traits.
        if ($phpcsFile->hasCondition($stackPtr, [T_CLASS, T_TRAIT]) === true) {
            return true;
        }

        return false;

    }//end shouldIgnoreUse()


}//end class
