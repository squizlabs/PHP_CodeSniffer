<?php
/**
 * Ensures that functions within functions are never used.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Standards\Squiz\Sniffs\PHP;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class InnerFunctionsSniff implements Sniff
{


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return [T_FUNCTION];

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

        $function = $phpcsFile->getCondition($stackPtr, T_FUNCTION);
        if ($function === false) {
            // Not a nested function.
            return;
        }

        $class = $phpcsFile->getCondition($stackPtr, T_ANON_CLASS);
        if ($class !== false && $class > $function) {
            // Ignore methods in anon classes.
            return;
        }

        $prev = $phpcsFile->findPrevious(T_WHITESPACE, ($stackPtr - 1), null, true);
        if ($tokens[$prev]['code'] === T_EQUAL) {
            // Ignore closures.
            return;
        }

        $error = 'The use of inner functions is forbidden';
        $phpcsFile->addError($error, $stackPtr, 'NotAllowed');

    }//end process()


}//end class
