<?php
/**
 * Ensures there is a single space before cast tokens.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Formatting;

use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Util\Tokens;

class SpaceBeforeCastSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return Tokens::$castTokens;

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
        $tokens = $phpcsFile->getTokens();

        if ($tokens[$stackPtr]['column'] === 1) {
            return;
        }

        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE) {
            $error = 'A cast statement must be preceded by a single space';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'NoSpace');
            if ($fix === true) {
                $phpcsFile->fixer->addContentBefore($stackPtr, ' ');
            }

            $phpcsFile->recordMetric($stackPtr, 'Spacing before cast statement', 0);
            return;
        }

        $phpcsFile->recordMetric($stackPtr, 'Spacing before cast statement', $tokens[($stackPtr - 1)]['length']);

        if ($tokens[($stackPtr - 1)]['column'] !== 1 && $tokens[($stackPtr - 1)]['length'] !== 1) {
            $error = 'A cast statement must be preceded by a single space';
            $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'TooMuchSpace');
            if ($fix === true) {
                $phpcsFile->fixer->replaceToken(($stackPtr - 1), ' ');
            }
        }

    }//end process()


}//end class
