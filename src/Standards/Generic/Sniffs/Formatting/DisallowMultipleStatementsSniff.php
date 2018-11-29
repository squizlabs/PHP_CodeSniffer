<?php
/**
 * Ensures each statement is on a line by itself.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class DisallowMultipleStatementsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_SEMICOLON];

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
        $tokens  = $phpcsFile->getTokens();
        $fixable = true;
        $prev    = $stackPtr;

        do {
            $prev = $phpcsFile->findPrevious([T_SEMICOLON, T_OPEN_TAG, T_OPEN_TAG_WITH_ECHO, T_PHPCS_IGNORE], ($prev - 1));
            if ($prev === false
                || $tokens[$prev]['code'] === T_OPEN_TAG
                || $tokens[$prev]['code'] === T_OPEN_TAG_WITH_ECHO
            ) {
                $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'no');
                return;
            }

            if ($tokens[$prev]['code'] === T_PHPCS_IGNORE) {
                $fixable = false;
            }
        } while ($tokens[$prev]['code'] === T_PHPCS_IGNORE);

        // Ignore multiple statements in a FOR condition.
        if (isset($tokens[$stackPtr]['nested_parenthesis']) === true) {
            foreach ($tokens[$stackPtr]['nested_parenthesis'] as $bracket) {
                if (isset($tokens[$bracket]['parenthesis_owner']) === false) {
                    // Probably a closure sitting inside a function call.
                    continue;
                }

                $owner = $tokens[$bracket]['parenthesis_owner'];
                if ($tokens[$owner]['code'] === T_FOR) {
                    return;
                }
            }
        }

        if ($tokens[$prev]['line'] === $tokens[$stackPtr]['line']) {
            $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'yes');

            $error = 'Each PHP statement must be on a line by itself';
            $code  = 'SameLine';
            if ($fixable === false) {
                $phpcsFile->addError($error, $stackPtr, $code);
                return;
            }

            $fix = $phpcsFile->addFixableError($error, $stackPtr, $code);
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->addNewline($prev);
                if ($tokens[($prev + 1)]['code'] === T_WHITESPACE) {
                    $phpcsFile->fixer->replaceToken(($prev + 1), '');
                }

                $phpcsFile->fixer->endChangeset();
            }
        } else {
            $phpcsFile->recordMetric($stackPtr, 'Multiple statements on same line', 'no');
        }//end if

    }//end process()


}//end class
