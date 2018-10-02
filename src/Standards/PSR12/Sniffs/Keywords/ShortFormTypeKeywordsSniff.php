<?php
/**
 * Verifies that the short form of type keywords is used (e.g., int, bool).
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Keywords;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ShortFormTypeKeywordsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [
            T_BOOL_CAST,
            T_INT_CAST,
        ];

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

        if (($tokens[$stackPtr]['code'] === T_BOOL_CAST
            && strtolower($tokens[$stackPtr]['content']) === '(bool)')
            || ($tokens[$stackPtr]['code'] === T_INT_CAST
            && strtolower($tokens[$stackPtr]['content']) === '(int)')
        ) {
            return;
        }

        $error = 'Short form type keywords must be used';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'LongFound');
        if ($fix === true) {
            if ($tokens[$stackPtr]['code'] === T_BOOL_CAST) {
                $phpcsFile->fixer->replaceToken($stackPtr, '(bool)');
            } else {
                $phpcsFile->fixer->replaceToken($stackPtr, '(int)');
            }
        }

    }//end process()


}//end class
