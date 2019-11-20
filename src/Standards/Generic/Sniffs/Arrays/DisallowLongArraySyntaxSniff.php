<?php
/**
 * Bans the use of the PHP long array syntax.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Arrays;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;

class DisallowLongArraySyntaxSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_ARRAY];

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

        $phpcsFile->recordMetric($stackPtr, 'Short array syntax used', 'no');

        $error = 'Short array syntax must be used to define arrays';

        if (isset($tokens[$stackPtr]['parenthesis_opener']) === false
            || isset($tokens[$stackPtr]['parenthesis_closer']) === false
        ) {
            // Live coding/parse error, just show the error, don't try and fix it.
            $phpcsFile->addError($error, $stackPtr, 'Found');
            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found');

        if ($fix === true) {
            $opener = $tokens[$stackPtr]['parenthesis_opener'];
            $closer = $tokens[$stackPtr]['parenthesis_closer'];

            $phpcsFile->fixer->beginChangeset();

            if ($opener === null) {
                $phpcsFile->fixer->replaceToken($stackPtr, '[]');
            } else {
                $phpcsFile->fixer->replaceToken($stackPtr, '');
                $phpcsFile->fixer->replaceToken($opener, '[');
                $phpcsFile->fixer->replaceToken($closer, ']');
            }

            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
