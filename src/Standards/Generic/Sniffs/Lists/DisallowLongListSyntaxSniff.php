<?php
/**
 * Bans the use of the PHP long list syntax.
 *
 * @author    Juliette Reinders Folmer <phpcs_nospam@adviesenzo.nl>
 * @copyright 2019 Juliette Reinders Folmer. All rights reserved.
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Generic\Sniffs\Lists;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class DisallowLongListSyntaxSniff implements Sniff
{


    /**
     * Registers the tokens that this sniff wants to listen for.
     *
     * @return int[]
     */
    public function register()
    {
        return [T_LIST];

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

        $next = $phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
        if ($next === false || $tokens[$next]['code'] !== T_OPEN_PARENTHESIS) {
            // Live coding or parse error.
            return;
        }

        $phpcsFile->recordMetric($stackPtr, 'Short list syntax used', 'no');

        $error = 'Long list syntax is not allowed';
        if (isset($tokens[$next]['parenthesis_closer']) === false) {
            // Live coding/parse error, just show the error, don't try and fix it.
            $phpcsFile->addError($error, $stackPtr, 'Found');
            return;
        }

        $fix = $phpcsFile->addFixableError($error, $stackPtr, 'Found');

        if ($fix === true) {
            $opener = $next;
            $closer = $tokens[$next]['parenthesis_closer'];

            $phpcsFile->fixer->beginChangeset();

            $phpcsFile->fixer->replaceToken($stackPtr, '');
            $phpcsFile->fixer->replaceToken($opener, '[');
            $phpcsFile->fixer->replaceToken($closer, ']');

            $phpcsFile->fixer->endChangeset();
        }

    }//end process()


}//end class
