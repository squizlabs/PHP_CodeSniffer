<?php
/**
 * Bans the use of the PHP short list syntax.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Lists;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Sniffs\TokenIs;

class DisallowShortListSyntaxSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_OPEN_SHORT_ARRAY];

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

        if (TokenIs::isShortList($phpcsFile, $stackPtr) === false) {
            // No need to examine nested subs of this short array.
            return $tokens[$stackPtr]['bracket_closer'];
        }

        $phpcsFile->recordMetric($stackPtr, 'Short list syntax used', 'yes');

        $error = 'Short list syntax is not allowed';
        $fix   = $phpcsFile->addFixableError($error, $stackPtr, 'Found');

        if ($fix === true) {
            $opener = $tokens[$stackPtr]['bracket_opener'];
            $closer = $tokens[$stackPtr]['bracket_closer'];

            $phpcsFile->fixer->beginChangeset();
            $phpcsFile->fixer->replaceToken($opener, 'list(');
            $phpcsFile->fixer->replaceToken($closer, ')');
            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
