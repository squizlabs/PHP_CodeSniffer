<?php
/**
 * Verifies that all class constants have their visibility set.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\PSR12\Sniffs\Properties;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;

class ConstantVisibilitySniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_CONST];

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
        if (PHP_VERSION_ID < 70100) {
            return;
        }

        $tokens = $phpcsFile->getTokens();
        $prev   = $phpcsFile->findPrevious(Tokens::$emptyTokens, ($stackPtr - 1), null, true);
        if (isset(Tokens::$scopeModifiers[$tokens[$prev]['code']]) === true) {
            return;
        }

        $error = 'Visibility must be declared on all constants if your project supports PHP 7.1 or later';
        $phpcsFile->addError($error, $stackPtr, 'NotFound');

    }//end process()


}//end class
