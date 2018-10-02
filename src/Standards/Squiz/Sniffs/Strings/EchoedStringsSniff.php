<?php
/**
 * Makes sure that any strings that are "echoed" are not enclosed in brackets.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\Strings;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class EchoedStringsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_ECHO];

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
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

        $firstContent = $phpcsFile->findNext(T_WHITESPACE, ($stackPtr + 1), null, true);
        // If the first non-whitespace token is not an opening parenthesis, then we are not concerned.
        if ($tokens[$firstContent]['code'] !== T_OPEN_PARENTHESIS) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around echoed strings', 'no');
            return;
        }

        $end = $phpcsFile->findNext([T_SEMICOLON, T_CLOSE_TAG], $stackPtr, null, false);

        // If the token before the semi-colon is not a closing parenthesis, then we are not concerned.
        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($end - 1), null, true);
        if ($tokens[$prev]['code'] !== T_CLOSE_PARENTHESIS) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around echoed strings', 'no');
            return;
        }

        // If the parenthesis don't match, then we are not concerned.
        if ($tokens[$firstContent]['parenthesis_closer'] !== $prev) {
            $phpcsFile->recordMetric($stackPtr, 'Brackets around echoed strings', 'no');
            return;
        }

        $phpcsFile->recordMetric($stackPtr, 'Brackets around echoed strings', 'yes');

        if (($phpcsFile->findNext(Tokens::$operators, $stackPtr, $end, false)) === false) {
            // There are no arithmetic operators in this.
            $error = 'Echoed strings should not be bracketed';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'HasBracket');
            if ($fix === true) {
                $phpcsFile->fixer->beginChangeset();
                $phpcsFile->fixer->replaceToken($firstContent, '');
                if ($tokens[($firstContent - 1)]['code'] !== T_WHITESPACE) {
                    $phpcsFile->fixer->addContent(($firstContent - 1), ' ');
                }

                $phpcsFile->fixer->replaceToken($prev, '');
                $phpcsFile->fixer->endChangeset();
            }
        }

    }//end process()


}//end class
