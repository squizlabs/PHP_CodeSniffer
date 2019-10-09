<?php
/**
 * Checks that control structures have boolean operators in the correct place.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\ControlStructures;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class BooleanOperatorPlacementSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_IF,
            T_WHILE,
            T_SWITCH,
            T_ELSEIF,
        ];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token
     *                                               in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
        ) {
            return;
        }

        $parenOpener = $tokens[$stackPtr]['parenthesis_opener'];
        $parenCloser = $tokens[$stackPtr]['parenthesis_closer'];

        if ($tokens[$parenOpener]['line'] === $tokens[$parenCloser]['line']) {
            // Conditions are all on the same line.
            return;
        }

        $find = [
            T_BOOLEAN_AND,
            T_BOOLEAN_OR,
        ];

        $operator  = $parenOpener;
        $position  = null;
        $error     = false;
        $operators = [];

        do {
            $operator = $phpcsFile->findNext($find, ($operator + 1), $parenCloser);
            if ($operator === false) {
                break;
            }

            $operators[] = $operator;

            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($operator - 1), $parenOpener, true);
            if ($prev === false) {
                // Parse error.
                return;
            }

            if ($tokens[$prev]['line'] < $tokens[$operator]['line']) {
                // The boolean operator is the first content on the line.
                if ($position === null) {
                    $position = 'first';
                }

                if ($position !== 'first') {
                    $error = true;
                }

                continue;
            }

            $next = $phpcsFile->findNext(T_WHITESPACE, ($operator + 1), $parenCloser, true);
            if ($next === false) {
                // Parse error.
                return;
            }

            if ($tokens[$next]['line'] > $tokens[$operator]['line']) {
                // The boolean operator is the last content on the line.
                if ($position === null) {
                    $position = 'last';
                }

                if ($position !== 'last') {
                    $error = true;
                }

                continue;
            }
        } while ($operator !== false);

        if ($error === false) {
            return;
        }

        $error = 'Boolean operators between conditions must be at the beginning or end of the line, but not both';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'FoundMixed');
        if ($fix === false) {
            return;
        }

        $phpcsFile->fixer->beginChangeset();
        foreach ($operators as $operator) {
            $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($operator - 1), $parenOpener, true);
            $next = $phpcsFile->findNext(T_WHITESPACE, ($operator + 1), $parenCloser, true);

            if ($position === 'last') {
                if ($tokens[$next]['line'] === $tokens[$operator]['line']) {
                    if ($tokens[$prev]['line'] === $tokens[$operator]['line']) {
                        // Move the content after the operator to the next line.
                        if ($tokens[($operator + 1)]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken(($operator + 1), '');
                        }

                        $first   = $phpcsFile->findFirstOnLine(T_WHITESPACE, $operator, true);
                        $padding = str_repeat(' ', ($tokens[$first]['column'] - 1));
                        $phpcsFile->fixer->addContent($operator, $phpcsFile->eolChar.$padding);
                    } else {
                        // Move the operator to the end of the previous line.
                        if ($tokens[($operator + 1)]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken(($operator + 1), '');
                        }

                        $phpcsFile->fixer->addContent($prev, ' '.$tokens[$operator]['content']);
                        $phpcsFile->fixer->replaceToken($operator, '');
                    }
                }//end if
            } else {
                if ($tokens[$prev]['line'] === $tokens[$operator]['line']) {
                    if ($tokens[$next]['line'] === $tokens[$operator]['line']) {
                        // Move the operator, and the rest of the expression, to the next line.
                        if ($tokens[($operator - 1)]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken(($operator - 1), '');
                        }

                        $first   = $phpcsFile->findFirstOnLine(T_WHITESPACE, $operator, true);
                        $padding = str_repeat(' ', ($tokens[$first]['column'] - 1));
                        $phpcsFile->fixer->addContentBefore($operator, $phpcsFile->eolChar.$padding);
                    } else {
                        // Move the operator to the start of the next line.
                        if ($tokens[($operator - 1)]['code'] === T_WHITESPACE) {
                            $phpcsFile->fixer->replaceToken(($operator - 1), '');
                        }

                        $phpcsFile->fixer->addContentBefore($next, $tokens[$operator]['content'].' ');
                        $phpcsFile->fixer->replaceToken($operator, '');
                    }
                }//end if
            }//end if
        }//end foreach

        $phpcsFile->fixer->endChangeset();

    }//end process()


}//end class
